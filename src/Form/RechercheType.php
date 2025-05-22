<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('depart', TextType::class, [
                'label' => 'Ville de départ',
                'required' => false
            ])
            ->add('arrivee', TextType::class, [
                'label' => 'Ville d\'arrivée',
                'required' => false
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de départ',
                'required' => false
            ])
            ->add('prixMax', NumberType::class, [
                'label' => 'Prix maximum (€)',
                'required' => false,
                'attr' => ['min' => 0]
            ])
            ->add('ecologique', CheckboxType::class, [
                'label' => 'Voyage écologique uniquement',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
