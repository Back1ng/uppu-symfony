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
}
