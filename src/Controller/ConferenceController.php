<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
    	$greet = '';
    	if ($name = $request->query->get('hello')) {
    		$greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));
		}
        /*return $this->render('conference/index.html.twig', [
            'controller_name' => 'ConferenceController',
        ]);*/
		$request->
        return new Response(<<<EOF

			<html>
				<body>
					$greet
					<img src="/images/uc.png" />
				</body>
			</html>
		EOF);
    }
}
