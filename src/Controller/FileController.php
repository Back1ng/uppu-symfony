<?php

namespace App\Controller;

use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
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
}
