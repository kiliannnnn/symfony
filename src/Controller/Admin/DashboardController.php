<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bachelor Symfony');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // public site link
        yield MenuItem::linkToUrl('Voir le site', 'fas fa-external-link-alt', $this->generateUrl('home'));
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', \App\Entity\User::class);

        // Articles submenu: list + create (frontend)
        yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Gérer les articles', 'fas fa-list', \App\Entity\Article::class),
            MenuItem::linkToRoute('Créer un article', 'fas fa-plus', 'article_new'),
        ]);

        // Comments with badge for unpublished
        $unpublishedCount = 0;
        try {
            $unpublishedCount = $this->getDoctrine()->getRepository(\App\Entity\Comment::class)->count(['isPublished' => false]);
        } catch (\Throwable $e) {
            // ignore if repository unavailable
        }

        $commentItem = MenuItem::linkToCrud('Commentaires', 'fas fa-comments', \App\Entity\Comment::class);
        if ($unpublishedCount > 0) {
            $commentItem = $commentItem->setBadge((string) $unpublishedCount)->setBadgePriority('danger');
        }
        yield $commentItem;
    }
}
