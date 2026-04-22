<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Rapport;
use App\Entity\User;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RapportController extends AbstractController
{
    private function currentUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    private function buildNav(string $activeRoute): array
    {
        $user = $this->currentUser();
        $role = $user->getRole();

        $items = [
            [
                'label' => 'Dashboard',
                'route' => 'user_ui_dashboard',
                'icon' => 'dashboard',
                'section' => 'home',
            ],
            [
                'label' => 'Profile',
                'route' => 'user_ui_profile',
                'icon' => 'person',
                'section' => 'home',
            ],
            [
                'label' => 'Settings',
                'route' => 'user_ui_settings',
                'icon' => 'settings',
                'section' => 'home',
            ],
        ];

        if ($role === 'PATIENT') {
            $items[] = [
                'label' => 'Doctors',
                'route' => 'app_doctors',
                'icon' => 'medical_services',
                'section' => 'modules',
            ];

            $items[] = [
                'label' => 'Mes rendez vous',
                'route' => 'app_patient_rdv',
                'icon' => 'calendar_month',
                'section' => 'modules',
            ];
        }

        if ($role === 'THERAPIST') {
            $items[] = [
                'label' => 'Gestion des rendez vous',
                'route' => 'app_therapist_rdv',
                'icon' => 'calendar_month',
                'section' => 'modules',
            ];
        }

        $items[] = [
            'label' => 'Exercises',
            'route' => 'user_ui_exercises',
            'icon' => 'fitness_center',
            'section' => 'modules',
        ];

        $items[] = [
            'label' => 'Forum',
            'route' => 'user_ui_forum',
            'icon' => 'forum',
            'section' => 'modules',
        ];

        return array_map(
            static function (array $item) use ($activeRoute): array {
                $item['children'] = $item['children'] ?? [];
                $item['active'] = $item['route'] === $activeRoute;

                return $item;
            },
            $items
        );
    }


#[Route('/rapport/{id}', name: 'app_rapport_show', methods: ['GET'])]
public function show(
    string $id,
    ConsultationRepository $consultationRepository,
    EntityManagerInterface $em
): Response {
    $user = $this->currentUser();
    $conn = $em->getConnection();

    $sql = "
        SELECT 
            u.id,
            u.email,
            p.firstName,
            p.lastName,
            p.phone,
            p.country,
            p.state,
            p.aboutMe
        FROM users u
        LEFT JOIN profiles p ON p.user_id = u.id
        WHERE u.id = :id
        LIMIT 1
    ";

    $patient = $conn->executeQuery($sql, [
        'id' => $id,
    ])->fetchAssociative();

    if (!$patient) {
        throw $this->createNotFoundException('Patient introuvable.');
    }

    $rapport = $em->getRepository(Rapport::class)->findOneBy([
        'patient' => $id,
    ]);

    if (!$rapport instanceof Rapport) {
        $userPatient = $em->getRepository(User::class)->find($id);

        $rapport = new Rapport();
        $rapport->setPatient($userPatient);

        $em->persist($rapport);
        $em->flush();
    }

    return $this->render('rapport/show.html.twig', [
        'rapport'       => $rapport,
        'patient'       => $patient,
        'consultations' => $consultationRepository->getConsultationsByRapoort($rapport),
        'nav'           => $this->buildNav('app_therapist_rdv'),
        'userName'      => $user->getEmail(),
    ]);
}


/* =========================================
   TRANSLATE API MYMEMORY
========================================= */
#[Route('/consultation/translate', name: 'app_consultation_translate', methods: ['POST'])]
public function translateConsultation(
    Request $request,
    HttpClientInterface $httpClient
): JsonResponse {

    $text = trim((string) $request->request->get('text'));
    $lang = trim((string) $request->request->get('lang', 'en'));

    if ($text === '') {
        return $this->json([
            'success' => false,
            'message' => 'Empty text'
        ]);
    }

    try {
        $response = $httpClient->request(
            'GET',
            'https://api.mymemory.translated.net/get',
            [
                'query' => [
                    'q' => $text,
                    'langpair' => 'fr|' . $lang
                ]
            ]
        );

        $data = $response->toArray(false);

        return $this->json([
            'success' => true,
            'translated' => $data['responseData']['translatedText'] ?? $text
        ]);

    } catch (\Throwable $e) {
        return $this->json([
            'success' => false,
            'translated' => $text
        ]);
    }
}






#[Route('/rapport/{id}/pdf', name: 'app_rapport_pdf', methods: ['GET'])]
public function exportPdf(
    string $id,
    ConsultationRepository $consultationRepository,
    EntityManagerInterface $em
): Response {
    $this->currentUser();

    $conn = $em->getConnection();

    /**
     * patient + profile
     */
    $sql = "
        SELECT 
            u.id,
            u.email,
            p.firstName,
            p.lastName,
            p.phone,
            p.country,
            p.state
        FROM users u
        LEFT JOIN profiles p ON p.user_id = u.id
        WHERE u.id = :id
        LIMIT 1
    ";

    $patient = $conn->executeQuery($sql, [
        'id' => $id,
    ])->fetchAssociative();

    if (!$patient) {
        throw $this->createNotFoundException('Patient introuvable.');
    }

    $rapport = $em->getRepository(Rapport::class)->findOneBy([
        'patient' => $id,
    ]);

    if (!$rapport instanceof Rapport) {
        throw $this->createNotFoundException('Rapport introuvable.');
    }

    $consultations = $consultationRepository->getConsultationsByRapoort($rapport);

    /**
     * HTML PDF
     */
    $html = $this->renderView('rapport/pdf.html.twig', [
        'patient'       => $patient,
        'rapport'       => $rapport,
        'consultations' => $consultations,
    ]);

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $fileName = 'rapport-medical-' . $patient['id'] . '.pdf';

    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]
    );
}











    #[Route('/consultation/new/{id}', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->currentUser();

        $rapport = $em->getRepository(Rapport::class)->find($id);

        if (!$rapport instanceof Rapport) {
            throw $this->createNotFoundException('Rapport introuvable.');
        }

        $consultation = new Consultation();
        $consultation->setRapport($rapport);
        $consultation->setDoctor($user);

        $form = $this->createForm(ConsultationType::class, $consultation, [
            'consultation' => $consultation,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($consultation);
            $em->flush();

            $this->addFlash('success', 'Consultation ajoutée avec succès.');

            return $this->redirectToRoute('app_rapport_show', [
                'id' => $rapport->getPatient()->getId(),
            ]);
        }

        return $this->render('rapport/new.html.twig', [
            'form'     => $form->createView(),
            'nav'      => $this->buildNav('app_therapist_rdv'),
            'userName' => $user->getEmail(),
        ]);
    }

    #[Route('/consultation/edit/{id}', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Consultation $consultation,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->currentUser();

        $form = $this->createForm(ConsultationType::class, $consultation, [
            'consultation' => $consultation,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Consultation modifiée avec succès.');

            return $this->redirectToRoute('app_rapport_show', [
                'id' => $consultation->getRapport()->getPatient()->getId(),
            ]);
        }

        return $this->render('rapport/edit.html.twig', [
            'form'         => $form->createView(),
            'consultation' => $consultation,
            'nav'          => $this->buildNav('app_therapist_rdv'),
            'userName'     => $user->getEmail(),
        ]);
    }

    #[Route('/consultation/delete/{id}', name: 'app_consultation_delete', methods: ['GET', 'POST'])]
    public function delete(
        Consultation $consultation,
        EntityManagerInterface $em
    ): Response {
        $user = $this->currentUser();

        $patientId = $consultation->getRapport()->getPatient()->getId();

        $em->remove($consultation);
        $em->flush();

        $this->addFlash('success', 'Consultation supprimée avec succès.');

        return $this->redirectToRoute('app_rapport_show', [
            'id' => $patientId,
        ]);
    }
}