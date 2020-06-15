<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Session;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Date',
                'html5' => false,
                'attr' => ['autocomplete' => false],
            ])
            ->add('noShow', CheckboxType::class, ['label' => 'No-show (-50%)', 'required' => false])
            ->add('student', EntityType::class, [
                'label' => 'Mentoré·e',
                'class' => Student::class,
                'choice_label' => 'details',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
