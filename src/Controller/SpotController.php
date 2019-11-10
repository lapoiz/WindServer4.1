<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Spot;
use App\Form\CommentaireType;
use App\Repository\SpotRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpotController extends AbstractController
{

    /**
     * @var SpotRepository
     */
    private $repositery;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(SpotRepository $repository, ObjectManager $em)
    {
        $this->repositery = $repository;
    }

    /**
     * @Route("/spot", name="spot.index")
     * @return Response
     */
    public function index() : Response
    {
        $spots = $this->repositery->findAll();

        $regions = array();
        foreach ($spots as $spot) {
            if (!array_key_exists($spot->getRegion()->getCodeRegion(),$regions)) {
                $regions[$spot->getRegion()->getCodeRegion()]=array("region"=>$spot->getRegion(), "spots" => array());
            }
            $regions[$spot->getRegion()->getCodeRegion()]["spots"][$spot->getCodeSpot()]=$spot;
        }

        return $this->render("spot/index.html.twig", [
            'current_menu' => 'spots',
            'spots' => $spots,
            'regions' => $regions,
            'urlImage' => $this->getParameter('rosace_directory').DIRECTORY_SEPARATOR,
        ]);
    }


    /**
     * @Route("/spot/{slug}-{id}", name="spot.show", requirements={"slug" : "[a-z1-9\-]*"})
     * @param Request $request
     * @return Response
     */
    public function show($slug, $id, Request $request) : Response
    {
        $spot = $this->repositery->find($id);
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        $urlImageMaree = $this->getParameter('maree_image_directory') . DIRECTORY_SEPARATOR . $spot->getId() . '.png';
        return $this->render("spot/show.html.twig", [
            'current_menu' => 'spot',
            'form' => $form->createView(),
            'urlMareeImage' => $urlImageMaree,
            'spot' => $spot
        ]);
    }

    /**
     * @Route("/spot/add/comment/{id}", name="spot_add_comment")
     * @param Spot $spot
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addComment(Spot $spot, Request $request, ObjectManager $manager) : Response
    {
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setIsVisible(false);
            $spot->addCommentaire($comment);
            $manager->persist($spot);
            $manager->persist($comment);
            $manager->flush();

            //Todo: envoyer mail sur commentaire

            $this->addFlash('success', 'Ajout du commentaire sur le Spot '.$spot->getName());
            return $this->redirectToRoute('spot.show', array("id"=> $spot->getId(), "slug"=> $spot->getSlug()));
        }

        return $this->render('spot/show.html.twig', [
            'spot' => $spot,
            'form' => $form->createView()
        ]);
    }

}