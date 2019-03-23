<?php

namespace App\Controller;

use App\Repository\SpotRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return $this->render("spot/index.html.twig.", [
            'current_menu' => 'spots',
            'spots' => $spots
        ]);
    }


    /**
     * @Route("/spot/{slug}-{id}", name="spot.show", requirements={"slug" : "[a-z1-9\-]*"})
     * @return Response
     */
    public function show($slug, $id) : Response
    {
        $spot = $this->repositery->find($id);

        return $this->render("spot/show.html.twig", [
            'current_menu' => 'spot',
            'spot' => $spot
        ]);
    }

}