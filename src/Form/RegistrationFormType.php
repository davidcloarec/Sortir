<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'label'=>"Adresse mail",
                'attr'=>['class'=>'form-control form-control-sm']
            ])
            ->add('username',TextType::class,[
                'label'=>"Pseudo",
                'attr'=>['class'=>'form-control form-control-sm'],
            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => false,
//                'label'=>"Accepter les conditions",
//                'attr'=>['class'=>'form-check-input'],
//                'constraints' => [
//                    new IsTrue([
//                        'message' => "Vous devez accepter les conditions d'utilisations.",
//                    ]),
//                ],
//            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password','class'=>'form-control form-control-sm'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => "Votre mot de passe doit être d'au moins {{ limit }} caractères",
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label'=>"Mot de passe",
            ])
            ->add('lastName',TextType::class,[
                'mapped'=>false,
                'label'=>'Nom',
                'attr'=>['class'=>'form-control form-control-sm'],
            ])
            ->add('firstName',TextType::class,[
                'mapped'=>false,
                'label'=>'Prénom',
                'attr'=>['class'=>'form-control form-control-sm'],
            ])
            ->add('phone',TextType::class,[
                'mapped'=>false,
                'label'=>'Téléphone',
                'attr'=>['class'=>'form-control form-control-sm'],
            ])
            ->add('admin',CheckboxType::class,[
                'mapped'=>false,
                'label'=>'Administrateur',
                'attr'=>['class'=>'form-check-input'],
                'label_attr'=>['class'=>'me-2'],
            ])
            ->add('active',CheckboxType::class,[
                'mapped'=>false,
                'label'=>'Actif',
                'attr'=>['class'=>'form-check-input'],
                'label_attr'=>['class'=>'me-2'],
            ])
            ->add('campus',EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'name',
                'label'=>"Campus",
                'multiple' => false,
                'expanded' => false,
                'mapped'=>false,
                'attr'=>['class'=>'form-control form-control-sm'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'cascade_validation' => true,
        ]);
    }
}