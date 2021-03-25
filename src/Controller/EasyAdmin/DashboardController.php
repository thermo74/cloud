<?php

namespace App\Controller\EasyAdmin;

use App\Entity\Categories;
use App\Entity\Files;
use App\Entity\ResetPasswordRequest;
use App\Entity\Roles;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/westudio-admin", name="easyadmin")
     * @return Response
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<a href="/"><img src="' . $_ENV['ROOT_URL'] . '/images/logo/logo.svg" style="width:150px;" alt="logo" /></a>')
            ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Files', 'fas fa-folder-open', Files::class)->setLinkTarget('_self');
        yield MenuItem::linkToCrud('Users', 'fas fa-folder-open', User::class)->setLinkTarget('_self');
        yield MenuItem::linkToCrud('Categories', 'fas fa-folder-open', Categories::class)->setLinkTarget('_self');
        yield MenuItem::linkToCrud('Roles', 'fas fa-folder-open', Roles::class)->setLinkTarget('_self');
        yield MenuItem::linkToCrud('Reset password Requests', 'fas fa-folder-open', ResetPasswordRequest::class)->setLinkTarget('_self');
    }
}
