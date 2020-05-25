<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ConferenceRepository;
use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class ConferenceController
 * @package App\Controller
 */
class ConferenceController extends AbstractController
{
	/**
	 * @var Environment
	 */
	private $twig;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * ConferenceController constructor.
	 * @param Environment $twig
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(Environment $twig, EntityManagerInterface $entityManager)
	{
		$this->twig = $twig;
		$this->entityManager = $entityManager;
	}

	/**
	 * @Route("/", name="homepage")
	 * @param ConferenceRepository $conferenceRepository
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function index(ConferenceRepository $conferenceRepository)
	{

		return new Response($this->twig->render('conference/index.html.twig'));

	}


	/**
	 * @Route("/conference/{slug}", name="conference")
	 * @param Request $request
	 * @param Conference $conference
	 * @param CommentRepository $commentRepository
	 * @param ConferenceRepository $conferenceRepository
	 * @param string $photoDir
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function show(Request $request, Conference $conference, CommentRepository $commentRepository, ConferenceRepository $conferenceRepository, string $photoDir)
	{
		$comment = new Comment();
		$form = $this->createForm(CommentFormType::class, $comment);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$comment->setConference($conference);
			if ($photo = $form['photo']->getData()) {
				$filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
				try {
					$photo->move($photoDir, $filename);
				} catch (FileException $e) {
					// unable to upload the photo, give up
				}
				$comment->setPhotoFilename($filename);
			}
			$this->entityManager->persist($comment);
			$this->entityManager->flush();
			return $this->redirectToRoute(
				'conference', ['slug' => $conference->getSlug()]);
		}

		$offset = max(0, $request->query->getInt('offset', 0));
		$paginator = $commentRepository->getCommentPaginator($conference, $offset);

		return new Response($this->twig->render('conference/show.html.twig', [
			'conferences' => $conferenceRepository->findAll(),
			'conference' => $conference,
			'comments' => $paginator,
			'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
			'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
			'comment_form' => $form->createView(),
		]));

	}
}
