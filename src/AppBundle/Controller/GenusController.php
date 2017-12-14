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
        // $genuses = $em->getRepository('AppBundle:Genus')->findAllPublishedOrderedBySize();
        $genuses = $em->getRepository('AppBundle:Genus')->findAllPublishedOrderedByRecentlyActive();

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

        $recentNotes = $em->getRepository('AppBundle:GenusNote')->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', [
            'genus' => $genus,
            'recentNotesCount' => count($recentNotes)
        ]);
    }

    /**
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {
        $notes = [];

        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/' . $note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('Y-m-d')
            ];
        }

        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
