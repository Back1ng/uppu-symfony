<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Service\Parser\CommentParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/file/{id}/comment/store", name="comment-store")
     */
    public function store(Request $request, EntityManagerInterface $em, string $id)
    {
        try {
            $parse = (new CommentParser())
                ->parse($request->get('message'));
        } catch (\Exception $e) {
            return $this->redirectToRoute("show_file", ['id' => $id]);
        }

        $comment = (new Comment())
            ->setFile($em->getRepository(File::class)->find($id))
            ->setMessage($parse->getMessage())
            ->setUserToken(hash("sha256",$request->getClientIp() . $request->headers->get('User-Agent')));

        $parsedID = (int)$parse->getId();
        if($parsedID && null !== $em->getRepository(Comment::class)->find($parsedID)) {
            $comment->setCommentParentId($parsedID);
        }

        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute("show_file", ['id' => $id]);
    }
}
