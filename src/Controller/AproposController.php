<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur dédié à la page "À propos de nous".
 */
class AproposController extends AbstractController
{
    /**
     * Affiche la page de présentation de l'entreprise Aktibat.
     *
     * @return Response
     */
    #[Route('/a-propos', name: 'app_apropos')]
    public function index(): Response
    {
        // Affiche la vue contenant les informations sur l'historique, les valeurs et l'équipe
        return $this->render('app/a_propos.html.twig');
    }
}
