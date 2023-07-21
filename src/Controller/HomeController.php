<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        $personnes = [
            [
                "prenom" => "Hocine",
                "nom" => "Boussaid",
                "age" => 33
            ],
            [
                "prenom" => "Danyl",
                "nom" => "Bekhoucha",
                "age" => 33
            ],
            [
                "prenom" => "Bérénice",
                "nom" => "Jarry",
                "age" => 23
            ]

        ];

        return $this->render('test.html.twig' , ['personnes' => $personnes]);
    }


    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $repo)
    {
        $produits = $repo->findBy([],['id' => 'DESC'], 5 );
        // dd($produits);

        return $this->render('home/home.html.twig', ['produits' => $produits]);
    }




}
