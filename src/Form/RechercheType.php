<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('depart', TextType::class, [
                'label' => 'Ville de départ'
            ])
            ->add('arrivee', TextType::class, [
                'label' => 'Ville d\'arrivée'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de départ'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
