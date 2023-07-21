<?php

namespace App\Controller;

use App\Form\AdminType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route(path: '/passer-en-admin-{id}', name: 'app_add_admin')]
    public function addAdmin($id, Request $request, UserRepository $repo)
    {
        $secret = "azerty";
        $user = $repo->find($id);

        $form = $this->createForm(AdminType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if( $form->get('mdpForm')->getData() == $secret) {
                $user->setRoles(['ROLE_ADMIN']);
                $repo->save($user,1);
                $this->addFlash('success', 'Félicitation vous êtes passé en admin!');
               
            }else{
                $this->addFlash('error', 'Vous n\'êtes pas authorisé à passer en admin !');
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/add_admin.html.twig', [
            'formAdmin' => $form->createView(),
            'user' => $user
        ]);

    }


}
