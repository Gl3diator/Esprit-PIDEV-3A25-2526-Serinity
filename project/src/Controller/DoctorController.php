<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DoctorController extends AbstractController
{
 
    #[Route('/doctors', name: 'app_doctors')]
    public function list(EntityManagerInterface $em): Response
    {
        $qb = $em->createQueryBuilder();
    
        $doctors = $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.role LIKE :role')
            ->setParameter('role', '%DOCTOR%')
            ->getQuery()
            ->getResult();
    
        return $this->render('doctor/list.html.twig', [
            'doctors' => $doctors
        ]);
    }

    

    #[Route('/rdv/save', name: 'app_rdv_save')]
    public function save(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $patientId = $session->get('user_id');
    
        $patient = $em->getRepository(User::class)->find($patientId);
        $doctor = $em->getRepository(User::class)->find($request->request->get('doctor_id'));
    
        $rdv = new RendezVous();
        $rdv->setPatient($patient);
        $rdv->setDoctor($doctor);
        $rdv->setMotif($request->request->get('motif'));
        $rdv->setDescription($request->request->get('description'));
        $rdv->setDateTime(new \DateTime($request->request->get('dateTime')));
    
        $em->persist($rdv);
        $em->flush();
    
        return $this->redirectToRoute('app_doctors');
    }




#[Route('/doctor/{id}', name: 'app_doctor_show')]
public function show(User $doctor): Response
{
    return $this->render('doctor/show.html.twig', [
        'doctor' => $doctor
    ]);
}




}
