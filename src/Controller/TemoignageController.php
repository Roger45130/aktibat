<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TemoignageController extends AbstractController
{
    #[Route('/temoignages', name: 'app_temoignages')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        CommentaireRepository $commentaireRepository,
        LoggerInterface $customLogger,
        SessionInterface $session
    ): Response {
        $this->addFlash('info', '🟡 Étape 1 : Entrée dans le contrôleur TemoignageController.');

        $commentaire = new Commentaire();

        // ➕ Étape 2 : pas besoin de vérifier si l'utilisateur est connecté

        // ➕ Étape 3 : création du formulaire
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        $customLogger->info('🟡 Étape 3 : Formulaire créé et handleRequest exécuté');

        // ➕ Étape 4 : test de soumission
        if ($form->isSubmitted()) {
            $this->addFlash('info', '🟡 Étape 4 : Formulaire soumis.');
            $customLogger->info('🟡 Étape 4 : Formulaire soumis.');

            if ($form->isValid()) {
                $this->addFlash('info', '🟢 Étape 5 : Formulaire valide. Enregistrement en base.');
                $customLogger->info('🟢 Étape 5 : Formulaire valide. Enregistrement en base.');

                $commentaire->setCreatedAt(new \DateTimeImmutable());
                $commentaire->setIsApproved(false);
                $commentaire->setIsTestimonial(true);

                // ➕ Ajout du nom d’utilisateur si connecté
                if ($this->getUser()) {
                    $commentaire->setUsername($this->getUser()->getUserIdentifier());
                }

                $em->persist($commentaire);
                $em->flush();

                $customLogger->info('🟢 Étape 6 : Témoignage enregistré avec succès en base.', [
                    'name' => $commentaire->getName(),
                    'message' => $commentaire->getMessage(),
                    'date' => $commentaire->getCreatedAt()->format('Y-m-d H:i:s')
                ]);

                $this->addFlash('success', 'Merci pour votre témoignage ! Il sera visible après validation.');

                return $this->redirectToRoute('app_temoignages');
            } else {
                $this->addFlash('danger', '🔴 Formulaire invalide.');
                $customLogger->error('🔴 Formulaire invalide.', [
                    'erreurs' => (string) $form->getErrors(true, false)
                ]);
            }
        } else {
            $customLogger->info('🟡 Étape 3.5 : Formulaire non soumis.');
        }

        // ➕ Étape 7 : récupération des témoignages validés
        $temoignages = $commentaireRepository->findBy([
            'isApproved' => true,
            'isTestimonial' => true,
        ], ['createdAt' => 'DESC']);

        $customLogger->info('🟡 Étape 7 : Chargement des témoignages validés. Nombre : ' . count($temoignages));

        return $this->render('app/temoignages.html.twig', [
            'form' => $form->createView(),
            'temoignages' => $temoignages,
        ]);
    }
}