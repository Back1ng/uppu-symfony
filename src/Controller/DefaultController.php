<?php
namespace App\Controller;

use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/files", name="files")
     */
    public function files()
    {
        $files = $this->getDoctrine()
            ->getRepository(File::class)
            ->findLastFiles();

        return $this->render('default/files.html.twig', [
            'files' => $files
        ]);
    }

    private function generateUniqueName() : string
    {
        $query = null;
        while([] !== $query) {
            $uniqueName = bin2hex(random_bytes(64));
            $query = $this->getDoctrine()
                ->getRepository(File::class)
                ->findBy(['name' => $uniqueName]);
        }
        return $uniqueName;
    }
}
