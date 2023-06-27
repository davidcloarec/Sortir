<?php

namespace App\Form;

use App\classe\Search;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
//                'label' => "Le nom de la sortie contient : ",
            'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Votre recherche...",
                    'class' => 'form-control-sm'
                ]
            ])
//            ->add('campus', EntityType::class, [
//                'label' => false,
//                'required' => false,
//                'class' => Campus::class,
//                'multiple' => false,
//                'expanded' => false,
//            ])
            ->add('submit', SubmitType::class, [
                'label' => "Rechercher",
                'attr' => [
                    'class' => 'btn-block btn-info',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
