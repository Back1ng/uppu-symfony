<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Service\FileUploader;
use App\Service\Writer\SizeWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param $uploadedDir
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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

                $uploadedFile = (new FileUploader(
                    $directory,
                    $this->generateUniqueName($directory))
                )->store($file);

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
        if (! $file = $this->getDoctrine()->getRepository(File::class)->find($id)) {
            return new Response("", 404);
        }

        $finder = new Finder();
        $finder->files()->name($file->getName())->in($file->getUploadedPath());
        if ($finder->hasResults()) {
            return $this->render('file/show.html.twig', [
                'file' => $file,
                'size' => (new SizeWriter(
                    $file->getSize()
                ))->write()
            ]);
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
            'Content-type' => $file->getMimeType(),
            'Content-length' => (int)$file->getSize(),
            'Accept-Ranges' => 'bytes',
        ]);
    }

    /**
     * @Route("/files/search", name="file-search")
     * @param Request $request
     */
    public function search(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $files = $entityManager->getRepository(File::class)
            ->createQueryBuilder('f')
            ->where('f.original_name LIKE :original_name')
            ->setParameter('original_name', "%".$request->get('search')."%")
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('file/files.html.twig', [
            'files' => $files
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
