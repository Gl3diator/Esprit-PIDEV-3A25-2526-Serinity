<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Rapport;
use App\Entity\User;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RapportController extends AbstractController
{
    #[Route('/rapport/{id}', name: 'app_rapport_show')]
    public function show(int $id, ConsultationRepository $cr , EntityManagerInterface $em): Response
    {
        $patient = $em->getRepository(User::class)->find($id);
    
        $rapport = $em->getRepository(Rapport::class)
            ->findOneBy(['patient' => $patient]);
    
        // 🔥 créer rapport si inexistant
        if (!$rapport) {
            $rapport = new Rapport();
            $rapport->setPatient($patient);
            $em->persist($rapport);
            $em->flush();
        }
    
        return $this->render('rapport/show.html.twig', [
            'rapport' => $rapport ,
            'consultations'=> $cr->getConsultationsByRapoort($rapport),
        ]);
    }


    #[Route('/consultation/new/{id}', name: 'app_consultation_new')]
public function new(int $id, Request $request, EntityManagerInterface $em): Response
{
        $userId = $request->getSession()->get('user_id');



    $rapport = $em->getRepository(Rapport::class)->find($id);

    $consultation = new Consultation();
    $consultation->setRapport($rapport);

    $consultation->setDoctor(  $em->getRepository(User::class)->find($userId) );

    $form = $this->createForm(ConsultationType::class, $consultation, [
        'consultation' => $consultation,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $em->persist($consultation);
        $em->flush();

        return $this->redirectToRoute('app_rapport_show', [
            'id' => $rapport->getPatient()->getId()
        ]);
    }

    return $this->render('rapport/new.html.twig', [
        'form' => $form->createView()
    ]);
}

    #[Route('/consultation/edit/{id}', name: 'app_consultation_edit')]
    public function edit(Consultation $consultation, Request $request, EntityManagerInterface $em): Response
    {
        $userId = $request->getSession()->get('user_id');

 

        $form = $this->createForm(ConsultationType::class, $consultation, [
            'consultation' => $consultation,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Consultation modifiee avec succes');

            return $this->redirectToRoute('app_rapport_show', [
                'id' => $consultation->getRapport()->getPatient()->getId(),
            ]);
        }

        return $this->render('rapport/edit.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }

    #[Route('/consultation/delete/{id}', name: 'app_consultation_delete')]
    public function delete(Consultation $consultation, Request $request, EntityManagerInterface $em): Response
    {
        $userId = $request->getSession()->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_login');
        }

        $patientId = $consultation->getRapport()->getPatient()->getId();

        $em->remove($consultation);
        $em->flush();

        $this->addFlash('success', 'Consultation supprimee avec succes');

        return $this->redirectToRoute('app_rapport_show', [
            'id' => $patientId,
        ]);
    }
}
