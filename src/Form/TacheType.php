<?php

namespace App\Form;

use App\Entity\Tache;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En cour' => 'en cour',
                    'Terminer' => 'terminer',
                    'à faire' => 'à faire'
                ]
            ])
            ->add('date_creation', DateType::class, [
                'widget' => 'choice'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
