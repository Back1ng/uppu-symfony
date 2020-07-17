<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
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
     * @Route("/file/{id}", name="show_file")
     */
    public function show($id)
    {
        if (! $fileEntity = $this->getDoctrine()->getRepository(File::class)->find($id)) {
            return new Response("", 404);
        }

        $finder = new Finder();
        $finder->files()->name($fileEntity->getName())->in($fileEntity->getUploadedPath());
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                return $this->render('file/show.html.twig', [
                    'file' => $fileEntity,
                    'fileStorage' => $file,
                ]);
            }
        }
        return new Response("", 404);
    }

    /**
     *@Route("/file/{id}/download", name="file-download")
     */
    public function download($id)
    {
        $fileEntity = $this->getDoctrine()->getRepository(File::class)->find($id);

        return $this->file($fileEntity->getUploadedPath().'/'.$fileEntity->getName(), $fileEntity->getOriginalName());
    }

    /**
     * @Route("/file/{id}/content", name="file-get-content")
     */
    public function content($id)
    {
        $file = $this->getDoctrine()->getRepository(File::class)->find($id);

        return new Response(file_get_contents($file->getUploadedPath().'/'.$file->getName()), 200, [
            'Content-type' => $file->getMimeType()
        ]);
    }
}
