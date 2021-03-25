<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PagesController
 * @package App\Controller
 */
class PagesController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexNoLocal(): RedirectResponse
    {
        return $this->redirectToRoute('app_login', [
            '_locale' => 'en'
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}", name="homepage")
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('app_login', [
            '_locale' => $request->getLocale()
        ]);
    }
}
