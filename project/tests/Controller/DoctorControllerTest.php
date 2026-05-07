<?php

namespace App\Tests\Controller;

use App\Entity\RendezVous;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DoctorControllerTest extends WebTestCase
{
    private function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        return $em;
    }

    private function findPatient(EntityManagerInterface $em): User
    {
        $patient = $em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.role NOT LIKE :therapist')
            ->setParameter('therapist', '%THERAPIST%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$patient instanceof User) {
            self::fail('Aucun patient trouvé dans la vraie base. Ajoute au moins un utilisateur non thérapeute.');
        }

        return $patient;
    }

    private function findDoctor(EntityManagerInterface $em): User
    {
        $doctor = $em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.role LIKE :therapist')
            ->setParameter('therapist', '%THERAPIST%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$doctor instanceof User) {
            self::fail('Aucun doctor / therapist trouvé dans la vraie base. Ajoute un utilisateur avec role THERAPIST.');
        }

        return $doctor;
    }

    public function testSaveRedirectsWhenDoctorNotFound(): void
    {
        $client = static::createClient();
        $em = $this->getEntityManager();

        $patient = $this->findPatient($em);

        $client->loginUser($patient);

        $client->request('POST', '/user/rdv/save', [
            'doctor_id' => '999999999',
            'motif' => 'Consultation',
            'description' => 'Description test',
            'dateTime' => '2026-07-10 10:00:00',
        ]);

        self::assertResponseRedirects('/user/doctors');
    }

    public function testSaveRedirectsWhenMotifIsEmpty(): void
    {
        $client = static::createClient();
        $em = $this->getEntityManager();

        $patient = $this->findPatient($em);
        $doctor = $this->findDoctor($em);

        $client->loginUser($patient);

        $client->request('POST', '/user/rdv/save', [
            'doctor_id' => (string) $doctor->getId(),
            'motif' => '',
            'description' => 'Description test',
            'dateTime' => '2026-07-10 10:00:00',
        ]);

        self::assertResponseRedirects('/user/doctors');
    }

    public function testSaveRedirectsWhenDateIsInvalid(): void
    {
        $client = static::createClient();
        $em = $this->getEntityManager();

        $patient = $this->findPatient($em);
        $doctor = $this->findDoctor($em);

        $client->loginUser($patient);

        $client->request('POST', '/user/rdv/save', [
            'doctor_id' => (string) $doctor->getId(),
            'motif' => 'Consultation',
            'description' => 'Description test',
            'dateTime' => 'bad-date',
        ]);

        self::assertResponseRedirects('/user/doctors');
    }

    public function testSaveCreatesAppointmentSuccessfully(): void
    {
        $client = static::createClient();
        $em = $this->getEntityManager();

        $patient = $this->findPatient($em);
        $doctor = $this->findDoctor($em);

        $uniqueMotif = 'Consultation urgente PHPUnit ' . uniqid('', true);

        $client->loginUser($patient);

        $client->request('POST', '/user/rdv/save', [
            'doctor_id' => (string) $doctor->getId(),
            'motif' => $uniqueMotif,
            'description' => 'Douleur forte',
            'dateTime' => '2026-07-10 10:00:00',
        ]);

        self::assertResponseRedirects('/user/doctors');

        /** @var RendezVous|null $rdv */
        $rdv = $em->getRepository(RendezVous::class)->findOneBy([
            'patient' => $patient,
            'doctor' => $doctor,
            'motif' => $uniqueMotif,
        ]);

        self::assertInstanceOf(RendezVous::class, $rdv);
        self::assertSame('Douleur forte', $rdv->getDescription());
        self::assertSame('2026-07-10 10:00:00', $rdv->getDateTime()->format('Y-m-d H:i:s'));

        /**
         * Nettoyage pour ne pas polluer la vraie base.
         */
        $em->remove($rdv);
        $em->flush();
    }
}