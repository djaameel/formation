<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/add_categorie', name: 'app_add_categorie')]
    public function add(CategorieRepository $repo, Request $request, SluggerInterface $slugger): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug( $slugger->slug( $categorie->getNom() ) );

            $repo->save($categorie,1);

            return $this->redirectToRoute("app_categories");
        }
        return $this->render('categorie/formCategorie.html.twig', [
            'formCategorie' => $form->createView()
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    public function allCategories(CategorieRepository $repo)
    {
        $categories = $repo->findAll();

        return $this->render('categorie/allCategories.html.twig', [
            'categories' => $categories
        ]);
    }


    #[Route('/update_categorie_{id}', name: 'app_update_categories')]
    public function update($id, CategorieRepository $repo, Request $request, SluggerInterface $slugger)
    {
        $categorie = $repo->find($id);
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $categorie->setSlug($slugger->slug($categorie->getNom()));

            $repo->save($categorie,1);

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categorie/formCategorie.html.twig', [ 
            'formCategorie' => $form->createView(),
            'categorie' => $categorie
            ] );

    }


    #[Route('/delete_categorie_{id}', name: 'app_delete_categories')]
    public function delete(CategorieRepository $repo)
    {
        $categorie = $repo->find($id);

        $repo->remove($categorie,1);

        return $this->redirectToRoute('app_categories');
    }


}
