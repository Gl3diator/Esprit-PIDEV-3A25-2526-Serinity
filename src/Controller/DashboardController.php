<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\RendezVous;
use App\Form\RendezVousAcceptType;

 final class DashboardController extends AbstractController
{
    private function getCurrentDoctor(Request $request, EntityManagerInterface $em): ?User
    {
        $userId = $request->getSession()->get('user_id');

        if (!$userId) {
            return null;
        }

        return $em->getRepository(User::class)->find($userId);
    }
 
    
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getCurrentDoctor($request, $em);

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $rdvs = $em->getRepository(RendezVous::class)->findBy(
            ['doctor' => $user],
            ['dateTime' => 'DESC']
        );

        $patients = [];

        foreach ($rdvs as $rdv) {
            if ($rdv->getPatient()) {
                $patients[$rdv->getPatient()->getId()] = $rdv->getPatient();
            }
        }

        $patients = array_values($patients);
    
        return $this->render('dashboard/index.html.twig', [
            'currentUser' => $user,
            'rdvs' => $rdvs,
            'patients' => $patients
        ]);
    }

    #[Route('/dashboard/rdv/{id}', name: 'app_dashboard_rdv_show')]
    public function showRdv(RendezVous $rdv, Request $request, EntityManagerInterface $em): Response
    {
        $doctor = $this->getCurrentDoctor($request, $em);

        if (!$doctor) {
            return $this->redirectToRoute('app_login');
        }

        if ($rdv->getDoctor()?->getId() !== $doctor->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('rdv/showadmin.html.twig', [
            'rdv' => $rdv,
        ]);
    }

    #[Route('/rdv/accept/{id}', name: 'app_rdv_accept')]
    public function accept(
        RendezVous $rdv,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $doctor = $this->getCurrentDoctor($request, $em);

        if (!$doctor) {
            return $this->redirectToRoute('app_login');
        }

        if ($rdv->getDoctor()?->getId() !== $doctor->getId()) {
            throw $this->createAccessDeniedException();
        }
    
$form = $this->createForm(RendezVousAcceptType::class, $rdv, [
    'rendez_vous' => $rdv,
]);    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            $rdv->setStatus('VALIDE');
    
            $em->flush();
    
            $this->addFlash('success', 'Rendez-vous validé avec modification');
    
            return $this->redirectToRoute('app_dashboard');
        }
    
        return $this->render('rdv/accept.html.twig', [
            'form' => $form->createView(),
            'rdv' => $rdv
        ]);
    }
#[Route('/rdv/refuse/{id}', name: 'app_rdv_refuse')]
public function refuse(RendezVous $rdv, Request $request, EntityManagerInterface $em): Response
{
    $doctor = $this->getCurrentDoctor($request, $em);

    if (!$doctor) {
        return $this->redirectToRoute('app_login');
    }

    if ($rdv->getDoctor()?->getId() !== $doctor->getId()) {
        throw $this->createAccessDeniedException();
    }

    $rdv->setStatus('REFUSE');
    $em->flush();

    return $this->redirectToRoute('app_dashboard');
}
}
