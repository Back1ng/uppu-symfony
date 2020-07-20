<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/file/{id}/comment/store", name="comment-store")
     */
    public function store(Request $request, string $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = (new Comment())
            ->setFile($em->getRepository(File::class)->find($id))
            ->setMessage($request->get('content'))
            ->setUserToken(md5($request->getClientIp()));
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute("show_file", [
            'id' => $id
        ]);
    }
}
