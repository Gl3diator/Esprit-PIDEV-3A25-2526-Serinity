<?php

namespace App\Form\Exercice;

use App\Entity\Exercice\Exercise;
use App\Entity\Exercice\Resource;
use App\Repository\Exercice\ExerciseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('exercise', EntityType::class, [
                'class' => Exercise::class,
                'label' => 'Exercice associe',
                'choice_label' => 'title',
                'placeholder' => 'Choisir un exercice',
                'query_builder' => static fn (ExerciseRepository $repository) => $repository->createQueryBuilder('e')->orderBy('e.title', 'ASC'),
                'required' => true,
                'attr' => [
                    'required' => true,
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de la ressource',
                'required' => true,
                'attr' => [
                    'required' => true,
                    'maxlength' => 255,
                    'placeholder' => 'Ex. Video guide, fiche PDF, meditation audio',
                ],
            ])
            ->add('mediaType', TextType::class, [
                'label' => 'Type de ressource',
                'required' => true,
                'attr' => [
                    'required' => true,
                    'maxlength' => 100,
                    'placeholder' => 'Ex. Video, PDF, Audio, lien',
                ],
            ])
            ->add('url', TextType::class, [
                'label' => 'Lien externe',
                'required' => false,
                'attr' => [
                    'maxlength' => 2000,
                    'placeholder' => 'https://...',
                ],
            ])
            ->add('durationSeconds', IntegerType::class, [
                'label' => 'Duree (secondes)',
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'max' => 14400,
                    'placeholder' => 'Ex. 180',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu ou consignes',
                'required' => false,
                'attr' => [
                    'maxlength' => 4000,
                    'rows' => 6,
                    'placeholder' => 'Ajoutez ici un texte, des etapes, ou un resume de la ressource.',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}
