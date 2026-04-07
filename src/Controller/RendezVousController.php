<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\RendezVousType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RendezVousController extends AbstractController
{
// src/Controller/RendezVousController.php

  
#[Route('/rdv/new/{id}', name: 'app_rdv_new')]
public function new(
    int $id,
    Request $request,
    EntityManagerInterface $em
): Response {

    $session = $request->getSession();
    $patientId = $session->get('user_id');

    if (!$patientId) {
        return $this->redirectToRoute('app_login');
    }

    $patient = $em->getRepository(User::class)->find($patientId);
    $doctor = $em->getRepository(User::class)->find($id);

    $rdv = new RendezVous();
    $rdv->setPatient($patient);
    $rdv->setDoctor($doctor);

    $form = $this->createForm(RendezVousType::class, $rdv);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $em->persist($rdv);
        $em->flush();

        $this->addFlash('success', 'Rendez-vous créé avec succès');

        return $this->redirectToRoute('app_patient_rdv');
    }

    return $this->render('rdv/new.html.twig', [
        'form' => $form->createView(),
        'doctor' => $doctor
    ]);
}

   
#[Route('/mes-rdv', name: 'app_patient_rdv')]
public function mesRdv(Request $request, EntityManagerInterface $em): Response
{
    $session = $request->getSession();
    $patientId = $session->get('user_id');

    if (!$patientId) {
        return $this->redirectToRoute('app_login');
    }

    $rdvs = $em->getRepository(RendezVous::class)->findBy(
        ['patient' => $patientId],
        ['dateTime' => 'DESC']
    );

    return $this->render('rdv/mes_rdv.html.twig', [
        'rdvs' => $rdvs
    ]);
}


#[Route('/rdv/edit/{id}', name: 'app_rdv_edit')]
public function edit(
    RendezVous $rdv,
    Request $request,
    EntityManagerInterface $em
): Response {
    $patientId = $request->getSession()->get('user_id');

    if (!$patientId) {
        return $this->redirectToRoute('app_login');
    }

    if (!$rdv->getPatient() || $rdv->getPatient()->getId() !== $patientId) {
        $this->addFlash('error', 'Vous ne pouvez pas modifier ce rendez-vous.');

        return $this->redirectToRoute('app_patient_rdv');
    }

    if ($rdv->getStatus() !== 'EN_ATTENTE') {
        $this->addFlash('error', 'Seuls les rendez-vous en attente peuvent etre modifies.');

        return $this->redirectToRoute('app_patient_rdv');
    }

    $form = $this->createForm(RendezVousType::class, $rdv);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $this->addFlash('success', 'Rendez-vous modifie avec succes.');

        return $this->redirectToRoute('app_patient_rdv');
    }

    return $this->render('rdv/edit.html.twig', [
        'form' => $form->createView(),
        'doctor' => $rdv->getDoctor(),
        'rdv' => $rdv,
    ]);
}






#[Route('/rdv/delete/{id}', name: 'app_rdv_delete')]
public function delete(RendezVous $rdv, EntityManagerInterface $em): Response
{
    $em->remove($rdv);
    $em->flush();

    return $this->redirectToRoute('app_patient_rdv');
}




#[Route('/rdv/show/rdv/{id}', name: 'app_rdv_show')]
public function show(RendezVous $rdv, EntityManagerInterface $em): Response
{
    // Implementation for showing rendez-vous details


    return $this->render('rdv/show.html.twig', [
        'rdv' => $rdv
    ]);}

 
}
