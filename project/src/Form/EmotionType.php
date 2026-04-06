<?php

namespace App\Form;

use App\Entity\Emotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
            'label' => 'Emotion name',
            'required' => false,
            'empty_data' => '',
         'attr' => [
        'maxlength' => 40,
        'placeholder' => 'Enter emotion name',
    ],
])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emotion::class,
        ]);
    }
}