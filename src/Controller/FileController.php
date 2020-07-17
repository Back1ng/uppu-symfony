<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Service\FileUploader;
use App\Service\Writer\SizeWriter;
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
    public function index(Request $request, $uploadedDir)
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

                $directory = $uploadedDir.'/'.date('Y-m-d');

                $uploadedFile = (new FileUploader($file, $directory, $this->generateUniqueName($directory)))->store();

                $entityManager->persist($uploadedFile);
                $entityManager->flush();

                return $this->redirectToRoute("show_file", ['id' => $uploadedFile->getId()]);
            }
        }

        return $this->render('file/index.html.twig', [
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

        return $this->render('file/files.html.twig', [
            'files' => $files
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
                    'size' => (new SizeWriter($file->getSize()))->write()
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

    private function generateUniqueName(string $directory) : string
    {
        $query = null;
        while([] !== $query) {
            $uniqueName = bin2hex(random_bytes(64));
            $query = $this->getDoctrine()
                ->getRepository(File::class)
                ->findBy([
                    'name' => $uniqueName,
                    'uploaded_path' => $directory
                ]);
        }
        return $uniqueName;
    }
}
