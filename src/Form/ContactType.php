<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [new Assert\Length(['max' => 255])],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['rows' => 8],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['min' => 10, 'max' => 5000])],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
