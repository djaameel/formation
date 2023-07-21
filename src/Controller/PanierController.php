<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/add-panier/{id}', name: 'app_panier_add')]
    public function addProduct($id, SessionInterface $session): Response
    {
// on recupére/crée le panier de/dans la session
        $panier = $session->get('panier', []);

        // si l'id existe dans le panier on increment la quantité qui est la valeur
        if( isset($panier[$id]) )
        {
            $panier[$id]++;
        }else // sinon on ajoute le produit pour la premiere fois
        {
            $panier[$id] = 1;
        }


        // on sauvegarde dans la session
        $session->set('panier', $panier);

        // dd($panier);


        return $this->redirectToRoute('app_panier_view');
    }


    #[Route('/panier', name: 'app_panier_view')]
    public function view(SessionInterface $session, ProduitRepository $repo)
    {
        $panier = $session->get('panier', []);

        $dataPanier = [];

        $total = 0;

        foreach ( $panier as $id => $quantite) {
                $produit = $repo->find($id);
                $dataPanier[] = [
                    'produit' => $produit,
                    'quantite' => $quantite
                ];

            $total += $produit->getPrix() * $quantite;   
        }

        return $this->render('panier/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total
        ]);

    }


}