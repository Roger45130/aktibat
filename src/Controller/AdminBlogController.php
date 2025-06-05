<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog', name: 'admin_blog_')]
class AdminBlogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(BlogRepository $blogRepository): Response
    {
        $blogs = $blogRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin_blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setCreatedAt(new \DateTimeImmutable());

            $imageFile = $form['imageFile']->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('blog_images_directory'), $newFilename);
                $blog->setImage($newFilename);
            }

            $em->persist($blog);
            $em->flush();

            $this->addFlash('success', 'Article ajouté avec succès.');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin_blog/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => false
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Blog $blog, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filesystem = new Filesystem();

            // Gestion de la suppression de l’image si la case est cochée
            if ($blog->getDeleteImage() && $blog->getImage()) {
                $imagePath = $this->getParameter('blog_images_directory') . '/' . $blog->getImage();
                if ($filesystem->exists($imagePath)) {
                    $filesystem->remove($imagePath);
                }
                $blog->setImage(null);
            }

            // Gestion du remplacement d’image si nouvelle image chargée
            $imageFile = $form['imageFile']->getData();
            if ($imageFile) {
                // Supprimer l’ancienne image
                if ($blog->getImage()) {
                    $oldImagePath = $this->getParameter('blog_images_directory') . '/' . $blog->getImage();
                    if ($filesystem->exists($oldImagePath)) {
                        $filesystem->remove($oldImagePath);
                    }
                }

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('blog_images_directory'), $newFilename);
                $blog->setImage($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Article mis à jour avec succès.');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin_blog/form.html.twig', [
            'form' => $form->createView(),
            'blog' => $blog,
            'editMode' => true
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Blog $blog, EntityManagerInterface $em): Response
    {
        $filesystem = new Filesystem();
        if ($blog->getImage()) {
            $imagePath = $this->getParameter('blog_images_directory') . '/' . $blog->getImage();
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
        }

        $em->remove($blog);
        $em->flush();

        $this->addFlash('danger', 'Article supprimé.');

        return $this->redirectToRoute('admin_blog_index');
    }

    #[Route('/delete-image/{id}', name: 'delete_image', methods: ['POST'])]
    public function deleteImage(Blog $blog, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image_' . $blog->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();
            $imagePath = $this->getParameter('blog_images_directory') . '/' . $blog->getImage();

            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $blog->setImage(null);
            $em->flush();

            $this->addFlash('info', 'Image supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_blog_edit', ['id' => $blog->getId()]);
    }
}
