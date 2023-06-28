<?php

namespace App\Form;

use App\classe\Search;
use App\Entity\Activity;
use App\Entity\Campus;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class SearchType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'label' => "Campus : ",
                'multiple' => false,
                'required' => false,
                'expanded'=> false,
                'mapped'=>false,
            ])
            ->add('string', TextType::class, [
                'label' => "Le nom de la sortie contient : ",
                'required' => false,
                'attr' => [
                    'placeholder' => "Votre recherche...",
                    'class' => 'form-control-sm'
                ]
            ])
//            ->add('startDate', DateType::class)
//            ->add('endDate', DateType::class)


            ->add('submit', SubmitType::class, [
                'label' => "Rechercher",
                'attr' => [
                    'class' => 'btn-block btn-info mt-3',
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
