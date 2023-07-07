<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Venue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label'=>'Nom du lieu'
            ])
            ->add('street', null, [
                'label'=>'Rue'
            ])
            ->add('latitude', null, [
                'label'=>'Latitude'
            ])
            ->add('longitude', null, [
                'label'=>'Longitude'
            ])
            ->add('city',EntityType::class,[
                'class'=>City::class,
                'label'=>'Ville',
                'mapped'=>false,
                'multiple'=>false,
                'expanded'=>false,
                'choice_label'=>'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Venue::class,
        ]);
    }
}