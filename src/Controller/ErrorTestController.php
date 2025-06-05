<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorTestController extends AbstractController
{
    #[Route('/erreur500', name: 'test_erreur_500')]
    public function erreur500(): Response
    {
        // Provoque une erreur 500 volontairement
        throw new \Exception("Erreur serveur test");
    }

    #[Route('/erreur404', name: 'test_erreur_404')]
    public function erreur404(): Response
    {
        throw new NotFoundHttpException("Page introuvable test");
    }
}
