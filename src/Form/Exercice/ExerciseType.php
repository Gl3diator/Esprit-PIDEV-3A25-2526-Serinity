<?php

namespace App\Form\Exercice;

use App\Entity\Exercice\Exercise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l exercice',
                'required' => true,
                'attr' => [
                    'required' => true,
                    'maxlength' => 255,
                    'placeholder' => 'Ex. Squat, pompes, gainage',
                ],
            ])
            ->add('type', TextType::class, [
                'label' => 'Type d exercice',
                'required' => true,
                'attr' => [
                    'required' => true,
                    'maxlength' => 100,
                    'placeholder' => 'Ex. Cardio, souplesse, respiration',
                ],
            ])
            ->add('level', IntegerType::class, [
                'label' => 'Niveau',
                'required' => true,
                'attr' => [
                    'required' => true,
                    'min' => 1,
                    'max' => 10,
                    'placeholder' => 'Ex. 1 a 10',
                ],
            ])
            ->add('durationMinutes', IntegerType::class, [
                'label' => 'Duree (minutes)',
                'required' => true,
                'attr' => [
                    'required' => true,
                    'min' => 1,
                    'max' => 300,
                    'placeholder' => 'Ex. 20',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'maxlength' => 2000,
                    'rows' => 6,
                    'placeholder' => 'Decrivez l exercice, ses objectifs et ses consignes...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercise::class,
        ]);
    }
}
