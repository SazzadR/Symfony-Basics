<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
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

        $genusNote = new GenusNote();
        $genusNote->setUsername('Aquaweaver');
        $genusNote->setUserAvatarFilename('rayan.jpeg');
        $genusNote->setNote('I counted 8 legs... as they wrapped around me');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->persist($genusNote);
        $em->flush();

        return new Response('<html><body>Saved new genus with id ' . $genus->getId() . '</body></html>');
    }

    /**
     * @Route("genus/")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')->findAllPublishedOrderedBySize();

        return $this->render('genus/list.html.twig', [
            'genesus' => $genuses
        ]);
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName)
    {
        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository('AppBundle:Genus')->findOneBy([
            'name' => $genusName
        ]);
        if (!$genus) {
            throw $this->createNotFoundException('No genus found!');
        }

        return $this->render('genus/show.html.twig', [
            'genus' => $genus
        ]);
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
