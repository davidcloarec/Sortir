<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Venue;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use function Sodium\add;

class ActivityType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        date_default_timezone_set('Europe/Paris');
        $today = new \DateTime();
        $tomorrow = $today->add(new \DateInterval('P1D'));

        $builder
            ->add('name', null, [
                'label'=>'Nom de la sortie : '
            ])
            ->add('startingTime', DateTimeType::class, [
                'label'=> 'Date et heure de la sortie : ',
                'widget'=>'single_text',
                'data'=> $today

            ])
            ->add('signUpLimit', DateType::class, [
                'label'=> 'Date limite de d\'inscription : ',
                'widget' => 'single_text',
                'attr'=>[
                    'min' => $tomorrow->format('Y-m-d'),
                    'value' => $tomorrow->format('Y-m-d')
                ]
            ])
            ->add('maxSignUp', null, [
                'label'=> 'Nombre de places : '
            ])
            ->add('duration', null, [
                'label' => 'Durée : '
            ])
            ->add('info', TextareaType::class, [
                'label' => 'Descriptions et infos : '
            ])
            ->add('campus', EntityType::class,[
                'class'=> Campus::class,
                'label'=>'Campus : ',
                'choice_label'=>'name'
            ])
            ->add('venue', EntityType::class, [
                'class'=> Venue::class,
                'label'=> 'Lieu :',
                'choice_label'=> 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
