<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1, 100));
        $genus->setSubfamily('Octopine'.rand(1, 20));
        $genus->setSpeciesCount(rand(1, 50));
        $hasFunFact = (bool) random_int(0, 1);
        if ($hasFunFact) {
            $genus->setFunFact('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam mollis tortor ut bibendum tristique.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->flush();

        return new Response('<html><body>Saved new genus with id ' . $genus->getId() . '</body></html>');
    }

    /**
     * @Route("genus/")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')->findAll();

        return $this->render('genus/list.html.twig', [
            'genesus' => $genuses
        ]);
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName)
    {
        $funFact = 'Octopuses can change the color of their body in just three-tenths of a second!';

        $this->get('logger')
            ->info('Showing genus: '.$genusName);

        return $this->render('genus/show.html.twig', array(
            'name' => $genusName,
            'funFact' => $funFact,
        ));
    }

    /**
     * @Route("/genus/{genusName}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction($genusName)
    {
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];
        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
