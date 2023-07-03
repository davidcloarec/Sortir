<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ParticipantType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $participant = $builder->getData();
        $username = '';

        if ($participant->getUser()) {
            $username = $participant->getUser()->getUsername();
        }
        $builder
            ->add('lastname',TextType::class,[
                'label'=>'Nom',
            ])
            ->add('firstname',TextType::class,[
                'label'=>'Prénom',
            ])
            ->add('phone',TextType::class,[
                'label'=>'Téléphone',
            ])
            ->add('mail',EmailType::class,[
                "label"=>"E-mail"
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label'=>"Campus",
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('username', TextType::class, [
                'mapped' => false, // Exclude from entity mapping
                'data' => $username,
                'label' => 'Pseudo',
            ])
            ->add('image', FileType::class, [
                'mapped'=> false,
                'label' => 'Ma photo',
                'required' => false,
            ]);
        if($participant->getUser()->getId()===$this->security->getUser()->getId()){
            $builder->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label'=>"Mot de passe",
                'first_options'  => ['label' => 'Nouveau mot de passe',],
                'second_options' => ['label' => 'Vérification du mot de passe'],
                'mapped' => false,
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}