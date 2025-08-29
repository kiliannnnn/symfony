<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre ne peut pas être vide.']),
                    new Assert\Length(['min' => 3, 'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.'])
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['rows' => 10],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le contenu ne peut pas être vide.']),
                    new Assert\Length(['min' => 10, 'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères.'])
                ],
            ])
            ->add('coverFile', FileType::class, [
                'label' => 'Image à la une (jpg, png)',
                'mapped' => true,
                'required' => false,
            ])
            ->add('existingCover', ChoiceType::class, [
                'label' => 'Choisir une image existante',
                'required' => false,
                // this field is not a property of the Article entity, don't map it
                'mapped' => false,
                // not mapped to the entity, rely on 'mapped' => false only
                'choices' => $options['existing_choices'] ?? [],
                'placeholder' => 'Aucune',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'existing_choices' => [],
        ]);
    }
}
