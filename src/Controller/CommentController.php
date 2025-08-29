<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}/edit', name: 'comment_edit')]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (! $user || $comment->getAuthor() === null || $comment->getAuthor()->getId() !== $user->getId()) {
            throw new AccessDeniedException('You can only edit your own comments.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // require moderation after edit
            $comment->setIsPublished(false);
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire mis à jour et soumis à modération.');
            return $this->redirectToRoute('article_show', ['slug' => $comment->getArticle()->getSlug()]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (! $user || $comment->getAuthor() === null || $comment->getAuthor()->getId() !== $user->getId()) {
            throw new AccessDeniedException('You can only delete your own comments.');
        }

        $submittedToken = $request->request->get('_token');
        if (! $this->isCsrfTokenValid('delete-comment'.$comment->getId(), $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('article_show', ['slug' => $comment->getArticle()->getSlug()]);
        }

        $slug = $comment->getArticle()->getSlug();
        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', 'Commentaire supprimé.');
        return $this->redirectToRoute('article_show', ['slug' => $slug]);
    }
}
