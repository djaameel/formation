<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    #[Route('/produits', name: 'app_produits')]
    public function allProducts(ProduitRepository $repo, CategorieRepository $repoCat): Response
    {
        $produits = $repo->findAll();
        $categories = $repoCat->findAll();

        return $this->render('article/allProducts.html.twig', [
            'produits' => $produits,
            'categories' => $categories
        ]);
    }

    #[Route('/produit{id}', name: 'app_produit')]
    public function oneProduct($id, ProduitRepository $repo, Request $request, CommentaireRepository $repoCom)
    {
        $produit = $repo->find($id);
        $commentaire= new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire); 

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $commentaire->setDateDeCreation(new DateTime('now'));
            $commentaire->setProduit($produit);

            $repoCom->save($commentaire,1);

            return $this->redirectToRoute('app_produit', ['id'=>$id]);
        }

        return $this->render('article/oneProduct.html.twig', [
            'produit' => $produit,
            'formCommentaire' => $form->createView()
        ]);
    }


    #[Route('/admin/add_produit', name: 'app_add_produit')]
    public function add(Request $request, ProduitRepository $repo, SluggerInterface $slugger)
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // on recupere la photo du champs "photo_form" du formulaire
            $file = $form->get('photo_form')->getData();
            $fileName = $slugger->slug( $produit->getTitre() ) . uniqid() . '.' . $file->guessExtension();

            try{
                // on deplace l'image uploadé dans le dossier configuré dans les parametres (voir service.yaml) avec le nom $fileName
                $file->move($this->getParameter('photos_produit'), $fileName);
            }catch(FileException $e){
                    // gestion des erreurs d'upload
            }

            // on affecte le nom de l'image '$fileName' à la propriété photo du produit pour l'envoie en bdd
            $produit->setPhoto($fileName);

            $repo->save($produit,1);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/formProduit.html.twig', [
            'formProduit' => $form->createView()
        ]);
        
    }

    #[Route('/delete_produit_{id}', name: 'app_delete_produit')]
    public function delete($id, ProduitRepository $repo)
    {
        // on recupere le rpoduit dont l'id est celui passé en parametre de la methode, qui lui même est recuperé depuis l'url
        $produit = $repo->find($id);

        // on supprime le produit en question de la bdd en utilisant la methode remove du repository 
        $repo->remove($produit,1);


        // une fois la supression terminé on redirige vers la page de tous les articles
        return $this->redirectToRoute("app_produits");

    }
    

    #[Route('/update_produit_{id}', name: 'app_update_produit')]
    public function update($id, ProduitRepository $repo, Request $request, SluggerInterface $slugger)
    {
        $produit = $repo->find($id);

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('photo_form')->getData();

            if($file)
            {
                $fileName = $slugger->slug( $produit->getTitre() ) . uniqid() . '.' . $file->guessExtension();

                try{
                    // on deplace l'image uploadé dans le dossier configuré dans les parametres (voir service.yaml) avec le nom $fileName
                    $file->move($this->getParameter('photos_produit'), $fileName);
                }catch(FileException $e){
                        // gestion des erreurs d'upload
                }
    
                // on affecte le nom de l'image '$fileName' à la propriété photo du produit pour l'envoie en bdd
                $produit->setPhoto($fileName);
            }

            $repo->save($produit,1);

            return $this->redirectToRoute('app_produits');
        }

        return $this->render('article/formProduit.html.twig', [
            'formProduit' => $form->createView()
        ]);
    }

    #[Route('/produits/{slug}', name: 'app_produits_categorie')]
    public function productsByCategory($slug, CategorieRepository $repo)
    {
        $categorie = $repo->findOneBy([ 'slug' => $slug ]);
        $produits = $categorie->getProduits();
        $categories = $repo->findAll();

        return $this->render('article/allProducts.html.twig', [
            'produits' => $produits,
            'categories' => $categories
        ]);
    }

}
