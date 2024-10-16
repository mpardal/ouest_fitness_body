<?php

namespace App\Form;

use App\Entity\Exhibitor;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'];
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse email.',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer une adresse email valide.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Entrez l\'adresse email'],
            ])
            ->add('password', PasswordType::class);
            if ($user->getRoles()[0] == 'ROLE_EXHIBITOR')
            {
                $builder
                    ->add('exhibitorGroup', EntityType::class, [
                        'label' => 'Groupe d\'exposant',
                        'class' => Exhibitor::class,
                        'choice_label' => 'name',
                    ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Associer le formulaire à l'entité Admin
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'user';
    }

}