<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Service\DirectoryManager;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, Filesystem $filesystem, $uploadedDir)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(FileType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->getData();

            foreach ($files as $file) {
                if (null === $file) {
                    return new Response("", 404);
                }

                $uploadedFile = (new FileUploader())
                    ->setDirectory($uploadedDir.'/'.date('Y-m-d'))
                    ->setFile($file)
                    ->setServerName($this->generateUniqueName())
                    ->store();

                $entityManager->persist($uploadedFile);
                $entityManager->flush();

                return $this->redirectToRoute("show_file", ['id' => $uploadedFile->getId()]);
            }
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

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
            $uniqueName = !bin2hex(random_bytes(64));
            $query = $this->getDoctrine()
                ->getRepository(File::class)
                ->findBy(['name' => $uniqueName]);
        }
        return $uniqueName;
    }
}
