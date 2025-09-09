<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('article')
                ->setCrudController(\App\Controller\Admin\ArticleCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title'])
                ->formatValue(function ($value) {
                    if ($value instanceof \App\Entity\Article) {
                        return $value->getTitle();
                    }
                    return (string) $value;
                }),
            AssociationField::new('author'),
            TextField::new('content')
                ->onlyOnIndex()
                ->formatValue(function ($value) {
                    $text = strip_tags((string) $value);
                    if (mb_strlen($text) > 140) {
                        return mb_substr($text, 0, 140) . '…';
                    }
                    return $text;
                }),
            TextEditorField::new('content')->onlyOnDetail(),
            DateTimeField::new('createdAt')->onlyOnIndex(),
            BooleanField::new('isPublished'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approve', 'Approuver')
            ->linkToCrudAction('approve')
            ->setHtmlAttributes(['onclick' => "return confirm('Approuver ce commentaire ?');"]); 
        $hide = Action::new('hide', 'Masquer')
            ->linkToCrudAction('hide')
            ->setHtmlAttributes(['onclick' => "return confirm('Masquer ce commentaire ?');"]); 
        return $actions
            ->add(Crud::PAGE_INDEX, $approve)
            ->add(Crud::PAGE_INDEX, $hide)
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) => $action->setHtmlAttributes(['onclick' => "return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');"]))
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->setLabel('Éditer'));
    }

    protected function resolveCommentFromContextOrRequest(AdminContext $context, EntityManagerInterface $em): ?Comment
    {
        $comment = null;
        $entityDto = $context->getEntity();
        if ($entityDto instanceof EntityDto) {
            $comment = $entityDto->getInstance();
        }

        if (! $comment) {
            $id = $context->getRequest()->query->get('entityId') ?? $context->getRequest()->get('entityId');
            if ($id) {
                $comment = $em->getRepository(Comment::class)->find($id);
            }
        }

        return $comment;
    }

    public function approve(AdminContext $context, EntityManagerInterface $em): RedirectResponse
    {
        $comment = $this->resolveCommentFromContextOrRequest($context, $em);
        if ($comment) {
            $comment->setIsPublished(true);
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($this->adminUrlGenerator->setController(self::class)->setAction('index')->generateUrl());
    }

    public function hide(AdminContext $context, EntityManagerInterface $em): RedirectResponse
    {
        $comment = $this->resolveCommentFromContextOrRequest($context, $em);
        if ($comment) {
            $comment->setIsPublished(false);
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($this->adminUrlGenerator->setController(self::class)->setAction('index')->generateUrl());
    }
}
