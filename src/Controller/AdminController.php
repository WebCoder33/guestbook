<?php


namespace App\Controller;
use App\Entity\Comment;
use App\Message\CommentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AdminController extends AbstractController
{
    private $twig;
    private $entityManager;
    private $bus;
    public function __construct(Environment $twig, EntityManagerInterface
    $entityManager, MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }
    /**
     * @Route("/admin/comment/review/{id}", name="review_comment")
     */
    public function reviewComment(Request $request, Comment $comment)
    {
        $accepted = !$request->query->get('reject');

        $this->bus->dispatch(new CommentMessage($comment->getId()));

        return $this->render('admin/review.html.twig', [
            'transition' => $transition,
            'comment' => $comment,
        ]);
    }
}