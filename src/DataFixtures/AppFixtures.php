<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=1; $i < 11; $i++) { 
            # code...
            $produit = new Produit();
            $produit->setTitre("produit$i")
                    ->setDescription("description du produit $i")
                    ->setCouleur("noir")
                    ->setTaille("L")
                    ->setPrix("19")
                    ->setStock("22");

            $manager->persist($produit);
        }

        $manager->flush();
    }
}
