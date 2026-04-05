<?php

namespace App\Form;

use App\Entity\Emotion;
use App\Entity\Influence;
use App\Entity\MoodEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoodEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entryDate', DateTimeType::class, [
                'label' => 'Entry date',
                'widget' => 'single_text',
            ])
            ->add('momentType', ChoiceType::class, [
                'label' => 'Moment type',
                'choices' => [
                    'Moment' => 'MOMENT',
                    'Day' => 'DAY',
                ],
            ])
            ->add('moodLevel', ChoiceType::class, [
                'label' => 'Mood level',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
            ])
            ->add('emotions', EntityType::class, [
                'class' => Emotion::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'label' => 'Emotions',
            ])
            ->add('influences', EntityType::class, [
                'class' => Influence::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'label' => 'Influences',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MoodEntry::class,
        ]);
    }
}