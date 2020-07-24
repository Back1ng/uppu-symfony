<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Service\FileUploader;
use App\Service\Writer\SizeWriter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $uploadedDir
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function index(Request $request, EntityManagerInterface $em, $uploadedDir)
    {
        $form = $this->createForm(FileType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData() as $file) {
                if (! $file) {
                    return new Response("", Response::HTTP_NOT_FOUND);
                }

                $directory = $uploadedDir.'/'.date('Y-m-d');

                $fu = new FileUploader($directory);
                try {
                    $uploadedFile = $fu->store($file, $this->generateUniqueName($directory));
                } catch (FileException $e) {
                    return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
                }

                $em->persist($uploadedFile);
                $em->flush();

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
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        if (! $file = $this->getDoctrine()->getRepository(File::class)->find($id)) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }

        $finder = new Finder();
        $sizeWriter = new SizeWriter();

        $finder->files()->name($file->getName())->in($file->getUploadedPath());
        if ($finder->hasResults()) {
            return $this->render('file/show.html.twig', [
                'file'     => $file,
                'size'     => $sizeWriter->write($file->getSize()),
                'comments' => $file->getComments()
            ]);
        }
        return new Response("", Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/file/{id}/download", name="file-download")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $fileEntity = $this->getDoctrine()->getRepository(File::class)->find($id);

        $originalFilename = $fileEntity->getUploadedPath().'/'.$fileEntity->getName();
        $downloadedName   = $fileEntity->getOriginalName();

        return $this->file($originalFilename, $downloadedName);
    }

    /**
     * @Route("/file/{id}/content", name="file-get-content")
     * @param $id
     * @return Response
     */
    public function content($id)
    {
        $file = $this->getDoctrine()->getRepository(File::class)->find($id);

        $fileSystem = new Filesystem();
        $fileName = $file->getUploadedPath().'/'.$file->getName();

        if (! $fileSystem->exists($fileName)) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }
        return new Response(
            file_get_contents($fileName),
            Response::HTTP_OK,
            [
                'Content-type' => $file->getMimeType(),
                'Content-length' => (int)$file->getSize(),
                'Accept-Ranges' => 'bytes',
            ]
        );
    }

    /**
     * @Route("/files/search", name="file-search")
     * @param Request $request
     */
    public function search(Request $request)
    {
        $files = $this->getDoctrine()
            ->getRepository(File::class)
            ->findByOriginalName($request->get('search'));

        return $this->render('file/files.html.twig', [
            'files' => $files
        ]);
    }

    private function generateUniqueName(string $directory) : string
    {
        $query = null;
        while([] !== $query) {
            $name = bin2hex(random_bytes(64));

            $query = $this->getDoctrine()
                ->getRepository(File::class)
                ->findBy([
                    'name' => $name,
                    'uploaded_path' => $directory
                ]);
        }
        return $name;
    }
}
