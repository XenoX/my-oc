<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EvaluationType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Date',
                'html5' => false,
                'attr' => ['autocomplete' => false],
            ])
            ->add('noShow', CheckboxType::class, ['label' => 'No-show (-50%)', 'required' => false])
            ->add('project', EntityType::class, [
                'query_builder' => static function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('p')
                        ->join('p.path', 'pa')
                        ->where('pa.user = :user')
                        ->setParameter('user', $user)
                    ;
                },
                'label' => 'Projet',
                'class' => Project::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
