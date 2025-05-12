<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Depart', TextType::class, [
                'label' => 'Ville de départ',
                'attr' => ['placeholder' => 'Ex : Paris']
            ])
            ->add('Arrivee', TextType::class, [
                'label' => 'Ville d’arrivée',
                'attr' => ['placeholder' => 'Ex : Lyon']
            ])
            ->add('dateDepart', DateTimeType::class, [
                'label' => 'Date et heure de départ',
                'widget' => 'single_text',
            ])
            ->add('dateArrivee', DateTimeType::class, [
                'label' => 'Date et heure d’arrivée',
                'widget' => 'single_text',
            ])
            ->add('placesDisponibles', IntegerType::class, [
                'label' => 'Nombre de places disponibles',
                'attr' => ['min' => 1]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix par personne (€)',
                'currency' => 'EUR'
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => 'modele',
                'label' => 'Véhicule utilisé',
                'placeholder' => 'Choisissez un véhicule'
            ])
            ->add('ecologique', CheckboxType::class, [
                'label' => 'Voyage écologique (voiture électrique)',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
