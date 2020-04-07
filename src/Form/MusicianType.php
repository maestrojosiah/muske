<?php

namespace App\Form;

use App\Entity\Musician;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MusicianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('roles')
            ->add('password')
            ->add('currentsalary')
            ->add('expectedsalary')
            ->add('fullname')
            ->add('email')
            ->add('phone')
            ->add('age')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Musician::class,
        ]);
    }
}
