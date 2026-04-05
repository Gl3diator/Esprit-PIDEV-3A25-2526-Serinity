<?php

namespace App\Form;

use App\Entity\JournalEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'Enter journal title',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'rows' => 8,
                    'placeholder' => 'Write your journal entry',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JournalEntry::class,
        ]);
    }
}