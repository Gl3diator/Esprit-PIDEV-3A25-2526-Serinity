<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class DoctorController extends AbstractController
{
    private function buildNav(string $activeRoute): array
    {
        $items = [
            ['label' => 'Dashboard', 'route' => 'user_ui_dashboard', 'icon' => 'dashboard', 'section' => 'home'],
            ['label' => 'Profile', 'route' => 'user_ui_profile', 'icon' => 'person', 'section' => 'home'],
            ['label' => 'Settings', 'route' => 'user_ui_settings', 'icon' => 'settings', 'section' => 'home'],
            ['label' => 'Doctors', 'route' => 'app_doctors', 'icon' => 'medical_services', 'section' => 'modules'],
            ['label' => 'Mes rendez vous', 'route' => 'app_patient_rdv', 'icon' => 'medical_services', 'section' => 'modules'],

            ['label' => 'Exercises', 'route' => 'user_ui_exercises', 'icon' => 'fitness_center', 'section' => 'modules'],
            ['label' => 'Forum', 'route' => 'user_ui_forum', 'icon' => 'forum', 'section' => 'modules'],
            ['label' => 'Mood', 'route' => 'user_ui_mood', 'icon' => 'mood', 'section' => 'modules'],
            ['label' => 'Sleep', 'route' => 'user_ui_sommeil_list', 'icon' => 'bedtime', 'section' => 'modules'],
        ];

        foreach ($items as &$item) {
            $item['active'] = $item['route'] === $activeRoute;
            $item['children'] = [];
        }

        return $items;
    }

    private function currentUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    #[Route('/doctors', name: 'app_doctors', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $user = $this->currentUser();

        $doctors = $em->createQueryBuilder()
            ->select('u', 'p')
            ->from(User::class, 'u')
            ->leftJoin('u.profile', 'p')
            ->where('u.role LIKE :role')
            ->setParameter('role', '%THERAPIST%')
            ->orderBy('p.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('doctor/list.html.twig', [
            'doctors' => $doctors,
            'nav' => $this->buildNav('app_doctors'),
            'userName' => $user->getEmail(),
        ]);
    }

    #[Route('/rdv/save', name: 'app_rdv_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em): Response
    {
        $patient = $this->currentUser();

        $doctorId = $request->request->get('doctor_id');
        $motif = trim((string) $request->request->get('motif'));
        $description = trim((string) $request->request->get('description'));
        $dateTime = (string) $request->request->get('dateTime');

        $doctor = $em->getRepository(User::class)->find($doctorId);

        if (!$doctor instanceof User) {
            $this->addFlash('error', 'Doctor not found.');

            return $this->redirectToRoute('app_doctors');
        }

        if ($motif === '') {
            $this->addFlash('error', 'Motif is required.');

            return $this->redirectToRoute('app_doctors');
        }

        try {
            $date = new \DateTime($dateTime);
        } catch (\Throwable) {
            $this->addFlash('error', 'Invalid date.');

            return $this->redirectToRoute('app_doctors');
        }

        $rdv = new RendezVous();
        $rdv->setPatient($patient);
        $rdv->setDoctor($doctor);
        $rdv->setMotif($motif);
        $rdv->setDescription($description);
        $rdv->setDateTime($date);

        $em->persist($rdv);
        $em->flush();

        $this->addFlash('success', 'Appointment created successfully.');

        return $this->redirectToRoute('app_doctors');
    }

    #[Route('/doctor/{id}', name: 'app_doctor_show', methods: ['GET'])] public function show(string $id, EntityManagerInterface $em): Response
    {
        $user = $this->currentUser();
        $doctor = $em->createQueryBuilder()->select('u', 'p')->from(User::class, 'u')->leftJoin('u.profile', 'p')->where('u.id = :id')->setParameter('id', $id)->getQuery()->getOneOrNullResult();
        if (!$doctor instanceof User) {
            throw $this->createNotFoundException('Doctor not found.');
        }
        $firstName = $doctor->getProfile()?->getFirstName() ?? '';
        $lastName = $doctor->getProfile()?->getLastName() ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        if ($fullName === '') {
            $fullName = $doctor->getEmail();
        }
        $phone = $doctor->getProfile()?->getPhone() ?? '';
        $email = $doctor->getEmail();
        $country = $doctor->getProfile()?->getCountry() ?? '';
        $state = $doctor->getProfile()?->getState() ?? '';
        $address = trim($country . ' ' . $state);    /**     * VCARD     */
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "N:{$lastName};{$firstName};;;\r\n";
        $vcard .= "FN:{$fullName}\r\n";
        $vcard .= "ORG:Serinity\r\n";
        $vcard .= "TITLE:Therapist\r\n";
        if ($phone !== '') {
            $vcard .= "TEL;TYPE=CELL:{$phone}\r\n";
        }
        $vcard .= "EMAIL:{$email}\r\n";
        if ($address !== '') {
            $vcard .= "ADR:;;{$address};;;;\r\n";
        }
        $vcard .= "END:VCARD";    /**     * OLD VERSION BUNDLE FIX     */
        $builder = new Builder(writer: new PngWriter(), data: $vcard, size: 260, margin: 10);
        $result = $builder->build();
        return $this->render('doctor/show.html.twig', ['doctor' => $doctor, 'qrCode' => $result->getDataUri(), 'nav' => $this->buildNav('app_doctors'), 'userName' => $user->getEmail(),]);
    }
}