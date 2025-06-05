<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commentaire', name: 'admin_commentaire_')]
class AdminCommentaireController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Affiche la liste des commentaires (articles + témoignages)
     */
    #[Route('/', name: 'index')]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findBy([], ['createdAt' => 'DESC']);
        $temoignages = $commentaireRepository->findBy(['isTestimonial' => true], ['createdAt' => 'DESC']);

        return $this->render('admin_commentaire/index.html.twig', [
            'commentaires' => $commentaires,
            'temoignages' => $temoignages,
        ]);
    }

    /**
     * Marque un commentaire comme approuvé
     */
    #[Route('/approve/{id}', name: 'approve', methods: ['GET'])]
    public function approve(Commentaire $commentaire, EntityManagerInterface $em): RedirectResponse
    {
        $commentaire->setIsApproved(true);
        $em->flush();

        $this->logger->info('✅ Commentaire approuvé', [
            'id' => $commentaire->getId(),
            'name' => $commentaire->getName(),
        ]);

        $this->addFlash('success', '✅ Commentaire approuvé avec succès.');

        return $this->redirectToRoute('admin_commentaire_index');
    }

    /**
     * Supprime un commentaire
     */
    #[Route('/delete/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Commentaire $commentaire, EntityManagerInterface $em): RedirectResponse
    {
        $id = $commentaire->getId();
        $name = $commentaire->getName();

        $em->remove($commentaire);
        $em->flush();

        $this->logger->warning('🗑️ Commentaire supprimé', [
            'id' => $id,
            'name' => $name,
        ]);

        $this->addFlash('danger', '🗑️ Commentaire supprimé.');

        return $this->redirectToRoute('admin_commentaire_index');
    }
}
