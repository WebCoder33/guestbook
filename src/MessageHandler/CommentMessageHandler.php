<?php

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;

class CommentMessageHandler implements MessageHandlerInterface
{
    private $spamChecker;
    private $entityManager;
    private $commentRepository;
    private $mailer;
    private $adminEmail;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        SpamChecker $spamChecker,
        CommentRepository $commentRepository,
        MailerInterface $mailer,
        string $adminEmail
    )
    {
        $this->entityManager = $entityManager;
        $this->spamChecker = $spamChecker;
        $this->commentRepository = $commentRepository;
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }
        $comment->setState('published');
        $this->entityManager->flush();
        $this->mailer->send((new NotificationEmail())
            ->subject('New comment posted')
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->from($this->adminEmail)
            ->to($this->adminEmail)
            ->context(['comment' => $comment])
        );
    }
}