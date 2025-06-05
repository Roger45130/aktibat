<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(BlogRepository $blogRepository): Response
    {
        $articles = $blogRepository->findBy(
            ['isApproved' => true],
            ['createdAt' => 'DESC']
        );

        return $this->render('app/blog.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_show')]
    public function show(
        BlogRepository $blogRepository,
        string $slug,
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        LoggerInterface $customLogger
    ): Response {
        $article = $blogRepository->findOneBy(['slug' => $slug, 'isApproved' => true]);

        if (!$article) {
            throw $this->createNotFoundException('Article introuvable ou non validé.');
        }

        $commentaire = new Commentaire();
        $commentForm = $this->createForm(CommentaireType::class, $commentaire);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $commentaire->setBlog($article);
            $commentaire->setCreatedAt(new \DateTimeImmutable());
            $commentaire->setIsApproved(false);

            // Récupération de l'utilisateur
            $user = $security->getUser();
            $commentaire->setUsername($user ? $user->getUserIdentifier() : 'Utilisateur');

            $em->persist($commentaire);
            $em->flush();

            // Enregistrement dans le log personnalisé
            $customLogger->info('Commentaire soumis sur l\'article "' . $article->getTitle() . '" par ' . $commentaire->getUsername(), [
                'slug' => $article->getSlug(),
                'message' => $commentaire->getMessage(),
                'date' => $commentaire->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);

            $this->addFlash('success', 'Votre commentaire a été soumis et sera publié après validation.');

            return $this->redirectToRoute('app_blog_show', ['slug' => $slug]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
