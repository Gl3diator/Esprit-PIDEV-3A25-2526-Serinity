<?php

namespace App\Form;

use App\Entity\Sommeil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SommeilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_nuit', DateType::class, [
                'label'  => 'Date de la nuit',
                'widget' => 'single_text',
                'attr'   => ['class' => 'form-control']
            ])
            ->add('heure_coucher', TextType::class, [
                'label' => 'Heure de coucher',
                'attr'  => ['class' => 'form-control', 'placeholder' => 'Ex: 22:30']
            ])
            ->add('heure_reveil', TextType::class, [
                'label' => 'Heure de réveil',
                'attr'  => ['class' => 'form-control', 'placeholder' => 'Ex: 07:00']
            ])
            ->add('qualite', ChoiceType::class, [
                'label'   => 'Qualité du sommeil',
                'choices' => [
                    'Excellente' => 'Excellente',
                    'Bonne'      => 'Bonne',
                    'Moyenne'    => 'Moyenne',
                    'Mauvaise'   => 'Mauvaise',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('commentaire', TextareaType::class, [
                'label'    => 'Commentaire',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('duree_sommeil', NumberType::class, [
                'label'    => 'Durée (heures)',
                'required' => false,
                'scale'    => 2,
                'attr'     => ['class' => 'form-control', 'step' => '0.5']
            ])
            ->add('interruptions', IntegerType::class, [
                'label'    => "Nombre d'interruptions",
                'required' => false,
                'attr'     => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('humeur_reveil', ChoiceType::class, [
                'label'    => 'Humeur au réveil',
                'required' => false,
                'choices'  => [
                    '😌 Reposé'  => '😌 Reposé',
                    '😄 Joyeux'  => '😄 Joyeux',
                    '😐 Neutre'  => '😐 Neutre',
                    '😴 Fatigué' => '😴 Fatigué',
                    'Énergisé'   => 'Énergisé',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('environnement', ChoiceType::class, [
                'label'    => 'Environnement',
                'required' => false,
                'choices'  => [
                    '🏠 Normal'      => '🏠 Normal',
                    '🌿 Calme'       => '🌿 Calme',
                    '😊 Confortable' => '😊 Confortable',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('temperature', NumberType::class, [
                'label'    => 'Température (°C)',
                'required' => false,
                'scale'    => 1,
                'attr'     => ['class' => 'form-control', 'step' => '0.1']
            ])
            ->add('bruit_niveau', ChoiceType::class, [
                'label'    => 'Niveau de bruit',
                'required' => false,
                'choices'  => [
                    '🔇 Silencieux' => '🔇 Silencieux',
                    '🔉 Léger'      => '🔉 Léger',
                    '🔉 Modéré'     => '🔉 Modéré',
                    '🔊 Fort'       => '🔊 Fort',
                ],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sommeil::class,
        ]);
    }
}