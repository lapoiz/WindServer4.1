<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Spot;
use App\Form\CommentaireType;
use App\Repository\SpotRepository;
use App\Service\DisplayObject;
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
    public function index(DisplayObject $displayObject) : Response
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
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'regions' => $regions,
            'urlImage' => $this->getParameter('rosace_directory').DIRECTORY_SEPARATOR,
        ]);
    }


    /**
     * @Route("/spot/{slug}-{id}", name="spot.show", requirements={"slug" : "[a-z1-9\-]*"})
     * @param Request $request
     * @return Response
     */
    public function show($slug, $id, Request $request, DisplayObject $displayObject) : Response
    {
        $spot = $this->repositery->find($id);
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        $urlImageMaree = $this->getParameter('maree_image_directory') . DIRECTORY_SEPARATOR .'maree.'. $spot->getId() . '.jpg';
        $spots = $this->repositery->findAll();
        return $this->render("spot/show.html.twig", [
            'current_menu' => 'spot',
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
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
    public function addComment(Spot $spot, Request $request, ObjectManager $manager, DisplayObject $displayObject) : Response
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

        $spots = $this->repositery->findAll();
        return $this->render('spot/show.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'form' => $form->createView()
        ]);
    }

}