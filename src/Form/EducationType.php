<?php

namespace App\Form;

use App\Entity\Education;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EducationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institution')
            ->add('fromyear', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('toyear'  , DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('coursename')
            ->add('degree')
            ->add('location')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Education::class,
        ]);
    }
}
