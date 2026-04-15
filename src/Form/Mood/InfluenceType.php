<?php

namespace App\Form\Mood;

use App\Entity\Mood\Influence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfluenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Influence name',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 60,
                    'placeholder' => 'Enter influence name',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Influence::class,
        ]);
    }
}