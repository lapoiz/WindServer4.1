<?php
namespace App\Controller\admin;


use App\Entity\Region;
use App\Form\RegionType;
use App\Form\SpotType;
use App\Form\WebsiteInfoSpotType;
use App\Repository\RegionRepository;
use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use App\Service\HTMLtoImage;
use App\Utils\RosaceWindManage;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Spot;

class AdminSpotController extends AbstractController
{

    /**
     * @var SpotRepository
     */
    private $repository;
    private $manager;

    /**
     * AdminSpotController constructor.
     * @param SpotRepository $repository
     * @param ObjectManager $manager
     */
    public function __construct(SpotRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin", name="admin_spot_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(DisplayObject $displayObject) : Response
    {
        $spots = $this->repository->findAll();
        $regionsNavBar = $displayObject->regionsForNavBar($spots);
        return $this->render("admin/spot/index.html.twig",
            compact('spots', 'regionsNavBar'));
    }

    /**
     * @Route("/admin/spot/create", name="admin_spot_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request, DisplayObject $displayObject) : Response
    {
        $spot = new Spot();
        $form = $this->createForm(SpotType::class, $spot);
        $form->handleRequest($request);

        RosaceWindManage::createRosaceWind($spot, $this->getParameter('rosace_directory_kernel'));

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($spot);
            $this->manager->flush();
            $this->addFlash('success', 'Spot crée avec succés');
            return $this->redirectToRoute('admin_spot_index');
        }
        $spots = $this->repository->findAll();
        return $this->render('admin/spot/new.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/spot/edit/{id}", name="admin_spot_edit")
     * @param Spot $spot
     * @param Request $request
     * @param HTMLtoImage $cardGenerator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Spot $spot, Request $request, HTMLtoImage $cardGenerator, DisplayObject $displayObject) : Response
    {
        $form = $this->createForm(SpotType::class, $spot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $spot = $form->getData();
            $this->manager->persist($spot);
            $this->manager->flush();

            // Créé l'image .svg dans repertoire définit dans config/services.yaml, utile pour inserer dans map France
            RosaceWindManage::createRosaceWind($spot, $this->getParameter('rosace_directory_kernel'));
            $cardGenerator->createImageCard($spot);

            $this->addFlash('success', 'Spot '.$spot->getName().', modifié avec succés');
            return $this->redirectToRoute('admin_spot_index');
        }

        $spots = $this->repository->findAll();
        return $this->render('admin/spot/edit.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            "urlCardImage" => $this->getParameter('card_image_directory'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/spot/delete/{id}", name="admin_spot_delete", methods="DELETE")
     * @param Spot $spot
     * @param Request $request
     * @return Response
     */
    public function delete(Spot $spot, Request $request) :Response
    {
        //if ($this->isCsrfTokenValid('delete' . $spot->getId(), $request->get('token'))) {
            $this->manager->remove($spot);
            $this->manager->flush();
            $this->addFlash('success', 'Spot supprimé avec succés');
        //}
        return $this->redirectToRoute("admin_spot_index");
    }


    /**
     * @Route("/admin/spot/websiteInfo/edit/{id}", name="admin_spot_websiteInfo_edit")
     * @param Spot $spot
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function websiteInfoEdit(Spot $spot, Request $request, DisplayObject $displayObject) : Response
    {
        $form = $this->createForm(WebsiteInfoSpotType::class, $spot);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $spot = $form->getData();

                $websiteInfos = $spot->getWebSiteInfos();
                foreach ($websiteInfos as $website) {
                    $website->setSpot($spot);
                    $this->manager->persist($website);
                }
                $this->manager->persist($spot);
                $this->manager->flush();

                $this->addFlash('success', 'Website info modifié avec succés');
            }
        }
        //$form->add('Save',SubmitType::class);

        $spots = $this->repository->findAll();
        return $this->render('admin/spot/websiteInfoEdit.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/spot/card/{id}", name="admin.spot.show.card")
     * @param Request $request
     * @return Response
     */
    public function showCard($id, Request $request) : Response
    {
        $spot = $this->repository->find($id);

        return $this->render("spot/card.html.twig", [
            'spot' => $spot,
            'urlRosaceImage' => $this->getParameter('rosace_directory').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/admin/spot/card/jpg/{id}", name="admin.spot.show.image.card")
     * @param Request $request
     * @return Response
     */
    public function showImageCard($id, HTMLtoImage $hTMLtoImage, Request $request) : Response
    {
        $spot = $this->repository->find($id);
        $hTMLtoImage->createImageCard($spot);

        $urlImage = $this->getParameter('card_image_directory').DIRECTORY_SEPARATOR.'card.'.$spot->getId().'.jpg';

        return $this->render("admin/spot/imageCard.html.twig", [
            'spot' => $spot,
            'urlImage' => $urlImage,
        ]);
    }


    /**
     * @Route("/admin/region/create", name="admin_region_new")
     * @param Request $request
     * @return Response
     */
    public function newRegion(Request $request,RegionRepository $regionRepository, DisplayObject $displayObject) : Response
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($region);
            $this->manager->flush();
            $this->addFlash('success', 'Region crée avec succés');
            return $this->redirectToRoute('admin_spot_index');
        }
        $spots = $this->repository->findAll();
        $regions = $regionRepository->findAll();

        return $this->render('admin/region/new.html.twig', [
            'region' => $region,
            'regions' => $regions,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'form' => $form->createView()
        ]);
    }

}