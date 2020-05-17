<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ConferenceRepository;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Environment $twig, ConferenceRepository $conferenceRepository)
    {
    	$greet = '';
    	if ($name = $request->query->get('hello')) {
    		$greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));
		}
        /*return $this->render('conference/index.html.twig', [
            'controller_name' => 'ConferenceController',
        ]);*/

        return new Response($twig->render('conference/index.html.twig', [
        	'conferences' => $conferenceRepository->findAll(),
		]));
        
    }
}
