<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\RendezVousAcceptType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DashboardController extends AbstractController
{
    private function currentUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    protected function buildNav(string $activeRoute): array
    {
        $user = $this->currentUser();
        $role = $user->getRole();

        $items = [
            [
                'label'   => 'Dashboard',
                'route'   => 'user_ui_dashboard',
                'icon'    => 'dashboard',
                'section' => 'home',
            ],
            [
                'label'   => 'Profile',
                'route'   => 'user_ui_profile',
                'icon'    => 'person',
                'section' => 'home',
            ],
            [
                'label'   => 'Settings',
                'route'   => 'user_ui_settings',
                'icon'    => 'settings',
                'section' => 'home',
            ],
        ];

        /**
         * ROLE PATIENT
         */
        if ($role === 'PATIENT') {
            $items[] = [
                'label'   => 'Doctors',
                'route'   => 'app_doctors',
                'icon'    => 'medical_services',
                'section' => 'modules',
            ];

            $items[] = [
                'label'   => 'Mes rendez vous',
                'route'   => 'app_patient_rdv',
                'icon'    => 'calendar_month',
                'section' => 'modules',
            ];
        }

        /**
         * ROLE THERAPIST
         */
        if ($role === 'THERAPIST') {
            $items[] = [
                'label'   => 'Gestion des rendez vous',
                'route'   => 'app_therapist_rdv',
                'icon'    => 'calendar_month',
                'section' => 'modules',
            ];
        }

        /**
         * COMMON
         */
        $items[] = [
            'label'   => 'Exercises',
            'route'   => 'user_ui_exercises',
            'icon'    => 'fitness_center',
            'section' => 'modules',
        ];

        $items[] = [
            'label'   => 'Forum',
            'route'   => 'user_ui_forum',
            'icon'    => 'forum',
            'section' => 'modules',
        ];

        $items[] = [
            'label'   => 'Mood',
            'route'   => 'user_ui_mood',
            'icon'    => 'mood',
            'section' => 'modules',
            'children' => [
                [
                    'label' => 'Mood entries',
                    'route' => 'user_ui_mood',
                    'icon'  => 'list',
                ],
                [
                    'label' => 'Journal',
                    'route' => 'user_ui_journal_entry',
                    'icon'  => 'edit_note',
                ],
                [
                    'label' => 'Insights',
                    'route' => 'user_ui_mood_insights',
                    'icon'  => 'insights',
                ],
                [
                    'label' => 'Recovery plan',
                    'route' => 'user_ui_mood_recovery_plan',
                    'icon'  => 'healing',
                ],
            ],
        ];

        $items[] = [
            'label'   => 'Sleep',
            'route'   => 'user_ui_sommeil_list',
            'icon'    => 'bedtime',
            'section' => 'modules',
            'children' => [
                [
                    'label' => 'Sommail',
                    'route' => 'user_ui_sommeil_list',
                    'icon'  => 'bedtime',
                ],
                [
                    'label' => 'Reves management',
                    'route' => 'user_ui_reve_index',
                    'icon'  => 'nights_stay',
                ],
            ],
        ];

        return array_map(
            static function (array $item) use ($activeRoute): array {
                $children = $item['children'] ?? [];

                $mappedChildren = array_map(
                    static fn(array $child): array => [
                        ...$child,
                        'active' => $child['route'] === $activeRoute,
                    ],
                    $children
                );

                $isChildActive = false;

                foreach ($mappedChildren as $child) {
                    if ($child['active'] === true) {
                        $isChildActive = true;
                        break;
                    }
                }

                return [
                    ...$item,
                    'children' => $mappedChildren,
                    'active'   => $item['route'] === $activeRoute || $isChildActive,
                ];
            },
            $items
        );
    }

     private function analyseEmergency(string $motif, string $description, HttpClientInterface $httpClient): array
    {
 
       

        $prompt = <<<PROMPT
You are a medical triage assistant.

Analyze this appointment request and return ONLY valid JSON.

Request motif: {$motif}
Request description: {$description}

Return exactly this JSON structure:
{
  "level": "low|medium|high|emergency",
  "title": "short title",
  "reason": "short explanation"
}

Rules:
- emergency: chest pain, stroke signs, severe breathing issue, heavy bleeding, loss of consciousness, suicidal intent
- high: strong acute symptoms needing fast review
- medium: moderate but not immediately life-threatening
- low: mild/non-urgent
- output ONLY JSON
PROMPT;

        try {
            $response = $httpClient->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . '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',
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'http://localhost',
                    'X-Title' => 'Serinity Therapist Dashboard',
                ],
                'json' => [
                    'model' => 'openai/gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.1,
                ],
            ]);

            $data = $response->toArray(false);
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $decoded = json_decode($content, true);

            if (!is_array($decoded) || !isset($decoded['level'])) {
                return [
                    'level' => 'unknown',
                    'title' => 'Invalid AI output',
                ];
            }

            return [
                'level' => $decoded['level'] ?? 'unknown',
                'title' => $decoded['title'] ?? '',
                'reason' => $decoded['reason'] ?? '',
            ];
        } catch (\Throwable) {
            return [
                'level' => 'unknown',
                'title' => 'AI unavailable',
            ];
        }
    }

    #[Route('/dashboard/therapist', name: 'app_therapist_rdv', methods: ['GET'])]
    public function index(EntityManagerInterface $em, HttpClientInterface $httpClient): Response
    {
        $user = $this->currentUser();
        $conn = $em->getConnection();

        $sqlRdvs = "
            SELECT
                r.id,
                r.date_time,
                r.status,
                r.motif,
                r.description,
                r.patient_id,
                u.email AS patient_email,
                p.firstName,
                p.lastName
            FROM rendez_vous r
            INNER JOIN users u ON u.id = r.patient_id
            LEFT JOIN profiles p ON p.user_id = u.id
            WHERE r.doctor_id = :doctorId
            ORDER BY r.date_time DESC
        ";

        $rdvs = $conn->executeQuery($sqlRdvs, [
            'doctorId' => $user->getId(),
        ])->fetchAllAssociative();

        foreach ($rdvs as &$rdv) {
            $rdv['ai_level'] = null;
            $rdv['ai_title'] = null;
            $rdv['ai_reason'] = null;

            if (($rdv['status'] ?? '') === 'EN_ATTENTE') {
                $analysis = $this->analyseEmergency(
                    (string) ($rdv['motif'] ?? ''),
                    (string) ($rdv['description'] ?? ''),
                    $httpClient
                );

                $rdv['ai_level'] = $analysis['level'] ?? 'unknown';
                $rdv['ai_title'] = $analysis['title'] ?? '';
                $rdv['ai_reason'] = $analysis['reason'] ?? '';
            }
        }
        unset($rdv);

        $sqlPatients = "
            SELECT DISTINCT
                u.id,
                u.email,
                p.firstName,
                p.lastName
            FROM rendez_vous r
            INNER JOIN users u ON u.id = r.patient_id
            LEFT JOIN profiles p ON p.user_id = u.id
            WHERE r.doctor_id = :doctorId
            ORDER BY p.firstName ASC
        ";

        $patients = $conn->executeQuery($sqlPatients, [
            'doctorId' => $user->getId(),
        ])->fetchAllAssociative();

        return $this->render('dashboard/index.html.twig', [
            'currentUser' => $user,
            'rdvs' => $rdvs,
            'patients' => $patients,
            'nav' => $this->buildNav('app_therapist_rdv'),
            'userName' => $user->getEmail(),
        ]);
    }


    #[Route('/dashboard/rdv/{id}', name: 'app_dashboard_rdv_show', methods: ['GET'])]
    public function showRdv(RendezVous $rdv): Response
    {
        $user = $this->currentUser();

        if ($rdv->getDoctor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('rdv/showadmin.html.twig', [
            'rdv'      => $rdv,
            'nav'      => $this->buildNav('app_therapist_rdv'),
            'userName' => $user->getEmail(),
        ]);
    }

    #[Route('/rdv/accept/{id}', name: 'app_rdv_accept', methods: ['GET', 'POST'])]
    public function accept(
        RendezVous $rdv,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->currentUser();

        if ($rdv->getDoctor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(RendezVousAcceptType::class, $rdv, [
            'rendez_vous' => $rdv,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rdv->setStatus('VALIDE');

            $em->flush();

            $this->addFlash('success', 'Rendez-vous validé avec succès.');

            return $this->redirectToRoute('app_therapist_rdv');
        }

        return $this->render('rdv/accept.html.twig', [
            'form'     => $form->createView(),
            'rdv'      => $rdv,
            'nav'      => $this->buildNav('app_therapist_rdv'),
            'userName' => $user->getEmail(),
        ]);
    }

    #[Route('/rdv/refuse/{id}', name: 'app_rdv_refuse', methods: ['GET', 'POST'])]
    public function refuse(
        RendezVous $rdv,
        EntityManagerInterface $em
    ): Response {
        $user = $this->currentUser();

        if ($rdv->getDoctor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $rdv->setStatus('REFUSE');

        $em->flush();

        $this->addFlash('success', 'Rendez-vous refusé.');

        return $this->redirectToRoute('app_therapist_rdv');
    }
}