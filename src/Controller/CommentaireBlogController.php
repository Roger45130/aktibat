<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Blog;
use App\Form\CommentaireType;
use App\Repository\BlogRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog', name: 'app_blog_')]
class CommentaireBlogController extends AbstractController
{
    #[Route('/{slug}', name: 'show')]
    public function show(
        string $slug,
        BlogRepository $blogRepository,
        Request $request,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        CommentaireRepository $commentaireRepository
    ): Response {
        $blog = $blogRepository->findOneBy(['slug' => $slug]);

        if (!$blog) {
            throw $this->createNotFoundException('Article introuvable.');
        }

        $commentaire = new Commentaire();
        $commentaire->setBlog($blog);
        $commentaire->setCreatedAt(new \DateTimeImmutable());
        $commentaire->setIsApproved(false);
        $commentaire->setIsTestimonial(false);

        // Si utilisateur connecté, on enregistre son username
        if ($this->getUser()) {
            $commentaire->setUsername($this->getUser()->getUserIdentifier());
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $logger->info('🟡 Formulaire commentaire blog soumis.');

            if ($form->isValid()) {
                $em->persist($commentaire);
                $em->flush();

                $this->addFlash('success', '🟢 Votre commentaire a bien été enregistré. Il apparaîtra après validation.');
                $logger->info('🟢 Commentaire blog enregistré.', [
                    'blog' => $blog->getId(),
                    'message' => $commentaire->getMessage(),
                    'name' => $commentaire->getName(),
                ]);

                return $this->redirectToRoute('app_blog_show', ['slug' => $slug]);
            } else {
                $this->addFlash('danger', '🔴 Formulaire invalide.');
                $logger->error('🔴 Formulaire commentaire blog invalide.', [
                    'erreurs' => (string) $form->getErrors(true, false),
                ]);
            }
        }

        // Récupération des commentaires approuvés
        $commentaires = $commentaireRepository->findBy([
            'blog' => $blog,
            'isApproved' => true,
            'isTestimonial' => false,
        ], ['createdAt' => 'DESC']);

        return $this->render('blog/show.html.twig', [
            'article' => $blog,
            'commentaires' => $commentaires,
            'commentForm' => $form->createView(),
        ]);
    }
}
