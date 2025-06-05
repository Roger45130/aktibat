<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;

class GalleryController extends AbstractController
{
    #[Route('/galerie', name: 'app_galerie')]
    public function galerie(): Response
    {
        $basePath = $this->getParameter('kernel.project_dir') . '/public/uploads/galerie';

        $galleryImages = $this->getImagesFromFolder($basePath);
        $debutImages = $this->getImagesFromFolder($basePath . '/debut');
        $avantDerniereImages = $this->getImagesFromFolder($basePath . '/avantDerniere');
        $derniereImages = $this->getImagesFromFolder($basePath . '/derniere');

        return $this->render('app/galerie.html.twig', [
            'gallery_images' => $galleryImages,
            'debut_images' => $debutImages,
            'avantDerniere_images' => $avantDerniereImages,
            'derniere_images' => $derniereImages
        ]);
    }

    private function getImagesFromFolder(string $folderPath): array
    {
        $finder = new Finder();
        $images = [];

        if (is_dir($folderPath)) {
            $finder->files()->in($folderPath)->name('/\.(jpg|jpeg|png|gif|webp)$/i');

            foreach ($finder as $file) {
                $images[] = [
                    'filename' => $file->getFilename(),
                    'description' => 'Image : ' . $file->getFilename(),
                    'alt' => pathinfo($file->getFilename(), PATHINFO_FILENAME)
                ];
            }
        }

        return $images;
    }
}
