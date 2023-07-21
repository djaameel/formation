<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeDetails;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\CommandeDetailsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/passer-commande', name: 'app_commande')]
    public function passerCommande(SessionInterface $session,
                                   CommandeRepository $comRepo, 
                                   CommandeDetailsRepository $detRepo,
                                   ProduitRepository $proRepo,
                                   EntityManagerInterface $manager): Response
    {
        $commande = new Commande();

        // on recupere l'utilisateur connecté
        $user = $this->getUser();

        if(!$user) // revient à dire if(!empty($user)) / si l'utilisateur n'est pas connecté
        {
            $this->addFlash("error", "veuillez vous connecter ou vous inscrire pour pouvoir passer commande !");
            return $this->redirectToRoute('app_panier_view');
        }

        $panier = $session->get('panier', []);

        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $proRepo->find($id);
            $dataPanier[] = [
                "produit" => $produit,
                "quantite" => $quantite,
                "sousTotal" => $produit->getPrix() * $quantite
            ];

            $total += $produit->getPrix() * $quantite;
        }

        // dd($dataPanier);
        $commande->setUser($user)
                  ->setMontant($total);
                 //On persiste mais sans le flush car il reste les details de la commande à faire
        $manager->persist($commande);

        foreach ($dataPanier as $data) {
            $commandeDetail  = new CommandeDetails();

            $commandeDetail->setQuantite($data ['quantite'])
                            ->setPrix($data ['sousTotal'])
                            ->setProduit ($data ['produit'])
                            ->setCommande($commande);

        
            $manager->persist($commandeDetail);
        }
        // ce flush aura pour but d'envoyer tout les objets persisté dans la bdd
        $manager->flush();
        // une fois la commande passée on retire le panier de la session
        $session->remove("panier");

        $this->addFlash("success", "Félcitation, votre commande à bien été prise en compte");
        return $this->redirectToRoute("app_home");

    }
    #[Route('/commande/{id}', name: 'app_commande_details')]
    public function detailsCommande($id, CommandeRepository $repo)
    {
        $commande = $repo->find($id);
        return $this->render('commande/detailsCommande.html.twig', [
            'commande' => $commande
        ]);
    }
    
}