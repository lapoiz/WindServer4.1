<?php
namespace App\Controller\admin;


use App\Form\SpotType;
use App\Form\WebsiteInfoSpotType;
use App\Form\WebsiteInfoType;
use App\Repository\SpotRepository;
use App\Utils\RosaceWindManage;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;
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
    public function index() : Response
    {
        $spots = $this->repository->findAll();
        return $this->render("admin/spot/index.html.twig", compact('spots'));
    }

    /**
     * @Route("/admin/spot/create", name="admin_spot_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request) : Response
    {
        $spot = new Spot();
        $form = $this->createForm(SpotType::class, $spot);
        $form->handleRequest($request);

        RosaceWindManage::createRosaceWind($spot, $this->getParameter('svg_directory'));

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($spot);
            $this->manager->flush();
            $this->addFlash('success', 'Spot crée avec succés');
            return $this->redirectToRoute('admin_spot_index');
        }

        return $this->render('admin/spot/new.html.twig', [
            'spot' => $spot,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/spot/edit/{id}", name="admin_spot_edit")
     * @param Spot $spot
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Spot $spot, Request $request) : Response
    {
        $form = $this->createForm(SpotType::class, $spot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $spot = $form->getData();
            $this->manager->persist($spot);
            $this->manager->flush();

            // Créé l'image .svg dans repertoire définit dans config/services.yaml, utile pour inserer dans map France
            RosaceWindManage::createRosaceWind($spot, $this->getParameter('svg_directory'));

            $this->addFlash('success', 'Spot '.$spot->getName().', modifié avec succés');
            return $this->redirectToRoute('admin_spot_index');
        }

        return $this->render('admin/spot/edit.html.twig', [
            'spot' => $spot,
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
    public function websiteInfoEdit(Spot $spot, Request $request) : Response
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

        return $this->render('admin/spot/websiteInfoEdit.html.twig', [
            'spot' => $spot,
            'form' => $form->createView()
        ]);
    }

}