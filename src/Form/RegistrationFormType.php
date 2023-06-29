<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('email')
            ->add('username')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('lastName',TextType::class,[
                'mapped'=>false,
                'label'=>'lastName'
            ])
            ->add('firstName',TextType::class,[
                'mapped'=>false,
                'label'=>'firstName'
            ])
            ->add('phone',TextType::class,[
                'mapped'=>false,
                'label'=>'phone'
            ])
            ->add('admin',CheckboxType::class,[
                'mapped'=>false,
                'label'=>'admin'
            ])
            ->add('active',CheckboxType::class,[
                'mapped'=>false,
                'label'=>'active'
            ])
            ->add('campus',EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'name',
                'label'=>"Campus",
                'multiple' => false,
                'expanded' => false,
                'mapped'=>false,
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