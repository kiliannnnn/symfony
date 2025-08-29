<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', TextareaType::class, [
            'label' => 'Votre commentaire',
            'attr' => ['rows' => 5, 'maxlength' => 1000],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le commentaire ne peut pas être vide.']),
                new Assert\Length(['min' => 10, 'max' => 1000, 'minMessage' => 'Votre commentaire est trop court.'])
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
