<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Consultation|null $currentConsultation */
        $currentConsultation = $options['consultation'];
        $currentRdvId = $currentConsultation?->getRendezVous()?->getId();
        $currentDoctorId = $currentConsultation?->getDoctor()?->getId();

        $builder
             ->add('diagnostic')
            ->add('prescription')
            ->add('notes')
            
             ->add('rendezVous', EntityType::class, [
        'class' => RendezVous::class,
        'choice_label' => function ($rdv) {
            return $rdv->getPatient()->getFullName() . ' - ' . $rdv->getDateTime()->format('d/m/Y H:i');
        },
        'query_builder' => function (EntityRepository $er) use ($currentRdvId, $currentDoctorId) {
            $qb = $er->createQueryBuilder('r')
                ->leftJoin('r.consultation', 'c');

            if ($currentDoctorId) {
                $qb->andWhere('r.doctor = :doctorId')
                    ->setParameter('doctorId', $currentDoctorId);
            }

            if ($currentRdvId) {
                $qb->andWhere('c.id IS NULL OR r.id = :currentRdvId')
                    ->setParameter('currentRdvId', $currentRdvId);
            } else {
                $qb->andWhere('c.id IS NULL');
            }

            return $qb;
        },
    ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
            'consultation' => null,
        ]);
    }
}
