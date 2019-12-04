<?php

namespace App\Controller;


use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SpotRepository $repository, DisplayObject $displayObject) : Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUsername();
        $spots = $repository->findAll();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUserName,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'error' => $error
            ]);
    }
}