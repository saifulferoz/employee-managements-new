<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('date_of_birth', DateType::class, ['label' => "Date Of Birth", 'attr' => []])
            ->add(
                'gender',
                ChoiceType::class,
                [
                    'choices' => [
                        "Male" => "M",
                        "Female" => "F",
                        "Others" => "O",
                    ],
                ]
            )
            ->add('photo', FileType::class, ['label' => "Profile Picture", 'data_class' => null])
            ->add('notes', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Employee::class,
            ]
        );
    }
}
