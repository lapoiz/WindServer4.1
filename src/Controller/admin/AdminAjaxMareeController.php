<?php
namespace App\Controller\admin;


use App\Entity\MareeRestriction;
use App\Entity\Spot;
use App\Form\MareeRestrictionType;
use App\Form\MareeType;
use App\Repository\MareeRestrictionRepository;
use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use App\Service\MareeToImage;
use App\Utils\GetDataMaree;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAjaxMareeController extends AbstractController
{

    /**
     * @var MareeRestrictionRepository
     */
    private $repository;
    private $manager;

    /**
     * AdminSpotController constructor.
     * @param MareeRestrictionRepository $repository
     * @param ObjectManager $manager
     */
    public function __construct(MareeRestrictionRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/maree/edit/{id}", name="admin_maree_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Spot $spot, Request $request, MareeToImage $mareetoImage, SpotRepository $spotRepository, DisplayObject $displayObject) : Response
    {
        $form = $this->createForm(MareeType::class, $spot);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $spot = $form->getData();

                $mareeRestrictions = $spot->getMareeRestriction();
                foreach ($mareeRestrictions as $restriction) {
                    $restriction->setSpot($spot);
                    $this->manager->persist($restriction);
                }
                $this->manager->persist($spot);
                $this->manager->flush();
                $mareetoImage->createImageMareeFromSpot($spot);
                $this->addFlash('success', 'Restriction sur les marée modifiée avec succés');

            }
        }
        //$form->add('Save',SubmitType::class);
        $spots = $spotRepository->findAll();
        return $this->render('maree/mareeEdit.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/ajax/maree/image/{id}", name="admin.ajax.maree.to.image")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateImage(Spot $spot, Request $request, MareeToImage $mareetoImage) : Response
    {

        $mareetoImage->createImageMaree($spot,
            $request->request->get('hauteurMHGrandeMaree'),
            $request->request->get('hauteurMBGrandeMaree'),
            $request->request->get('hauteurMHPetiteMaree'),
            $request->request->get('hauteurMBPetiteMaree'),
            $request->request->get('hauteurMHMoyenneMaree'),
            $request->request->get('hauteurMBMoyenneMaree'),
            $request->request->get('contraintes'));
        $this->addFlash('success', 'Image générée');
        return new JsonResponse("jobDone!");
    }

    /**
     * @Route("/admin/ajax/maree/delete/{id}", name="admin_ajax_maree_delete")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Spot $spot, Request $request) : Response
    {
        $form = $this->createForm(MareeType::class, $spot);
        $this->addFlash('success', 'nous n avons rien fait !!!!');
        return $this->render('maree/restrictionMareeForm.html.twig', [
            'spot' => $spot,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/ajax/maree/getcoef/{idURLInfoMaree}", name="admin_ajax_maree_getcoef")
     * @param null $idURLInfoMaree
     * @param Request $request
     * @return JsonResponse
     */
    public function getDateCoefAction($idURLInfoMaree=null, Request $request) : JsonResponse
    {
        // Récupere la liste des amplitudes de marée: http://maree.info/$idURLInfoMaree/calendrier
        // Parse le tableau et récupére URL de la marée à coef le plus haut, de la marée le coef le plus bas, et de la marée à coef 80
        // sur la base de :
        // Tous les Table classe="CalendrierMois"
        //  Pour chaque TR
        //      récupére les TD class="coef"
        //          compare avec max, min et 80
        //          si OK
        //              get id de TD class="event" (en enlevant le D du début)
        //              get title de TD class="DW"

        // envoie les jours en JSON

        return new JsonResponse(GetDataMaree::getExtremMaree($idURLInfoMaree));

    }

    /**
     * @Route("/admin/ajax/maree/getforday/{idURLInfoMaree}/{idDateURLInfoMaree}", name="admin_ajax_maree_getforday")
     * @param null $idURLInfoMaree
     * @param null $idDateURLInfoMaree
     * @param Request $request
     * @return JsonResponse
     */
    public function getMareeForDayAction($idURLInfoMaree=null,$idDateURLInfoMaree=null, Request $request) : JsonResponse
    {
        // Récupere la page: http://maree.info/$idURLInfoMaree?d=$idDateURLInfoMaree
        // Parse avec ce qui est déjà fait dans core -> MareeGetData
        // envoie la hauteur marée haute et marée basse en JSON
        return new JsonResponse(GetDataMaree::getHauteurMaree($idURLInfoMaree, $idDateURLInfoMaree));
    }

}