<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->setAction($options['action']) // Set form action dynamically
        ->add('username', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Username is required.']),
                new Length(['min' => 3, 'minMessage' => 'Username must be at least 3 characters.']),
                new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Username can only contain letters.'
                    ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Email is required.']),
                new Email(['message' => 'Enter a valid email address.']),
            ],
        ])
        ->add('mobile', TelType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Mobile number is required.']),
                new Regex([
                    'pattern' =>'/^\d{10}$/',
                    'message' => 'Mobile must contain 10 digits.']),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'action' => '/add/new/user',
            'csrf_protection' => false,
        ]);
    }
}
