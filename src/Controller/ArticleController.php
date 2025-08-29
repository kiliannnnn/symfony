<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CommentType;
use App\Entity\Comment;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'article_list')]
    public function list(ArticleRepository $articleRepository): Response
    {
    // This method will be replaced below by the paginated article list.
    return $this->redirectToRoute('article_list');
    }

    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $article = new Article();
        // scan uploads/articles for existing files
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/articles';
        $existing = [];
        if (is_dir($uploadsDir)) {
            $files = array_values(array_filter(scandir($uploadsDir), fn($f) => ! in_array($f, ['.', '..'])));
            foreach ($files as $f) {
                $existing[$f] = $f;
            }
        }

        $form = $this->createForm(ArticleType::class, $article, ['existing_choices' => $existing]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTimeImmutable());
            // if an existing cover was selected, set it
            $existingCover = $form->get('existingCover')->getData();
            if ($existingCover) {
                $article->setCover($existingCover);
            }
            if (! $article->getSlug()) {
                $slug = $slugger->slug($article->getTitle())->lower();
                $article->setSlug((string) $slug);
            }
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès.');
            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles', name: 'article_list')]
    public function listPaginated(Request $request, ArticleRepository $articleRepository): Response
    {
        $qb = $articleRepository->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC');

        if ($q = $request->query->get('q')) {
            $qb->andWhere('a.title LIKE :q OR a.content LIKE :q')
               ->setParameter('q', '%'.$q.'%');
        }

        // count total
    $countQb = clone $qb;
    // Remove ordering from count query to avoid Postgres grouping error
    $countQb->resetDQLPart('orderBy');
    $countQb->select('COUNT(a.id)');
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 10;

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $articles = $query->getResult();

        $totalPages = (int) max(1, ceil($total / $perPage));

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    #[Route('/article/{slug}', name: 'article_show')]
    public function show(string $slug, ArticleRepository $articleRepository, Request $request, EntityManagerInterface $em): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug]);
        if (! $article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            if (! $this->getUser()) {
                return $this->redirectToRoute('app_login');
            }

            $comment->setAuthor($this->getUser());
            $comment->setArticle($article);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setIsPublished(false); // default to false for moderation

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été soumis et attend modération.');
            return $this->redirectToRoute('article_show', ['slug' => $slug]);
        }

        // prev/next simple placeholders
        $prevArticle = null;
        $nextArticle = null;

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView(),
            'prevArticle' => $prevArticle,
            'nextArticle' => $nextArticle,
        ]);
    }
}
