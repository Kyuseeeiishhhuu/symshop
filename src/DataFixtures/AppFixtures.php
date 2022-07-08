<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Faker\Factory;
use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        
        //Instanciation de Faker (generateur de fausses donnes realiste )
        $faker = Factory::create();

        for($j = 0; $j <=4; $j++)
        {

            //Ici on creer une boucle pour generer 5 categories

        $categorie = new Categorie();

        $categorie->setNom($faker->sentence())
                    ->setSlug($this->slugger->slug($categorie->getNom()));
            
            $manager->persist($categorie);


            for($i = 1; $i <= mt_rand(5 ,30); $i++)
            {
            /*Les fixtures permettent de simuler un jeux de fausses donnees 
            */
            /* Ici on simule un jeu de 15 produits*/

            /*A chaque tour de boucle un instance (on creer) un nouveau produit , que l'on remplira avec les setters*/

            $produit = new Produit();

            $produit->setNom("Produit n° $i")
                    ->setDescription("Voici la description du produit $i")
                    ->setPrix(mt_rand(15, 89))
                    ->setImage("https://picsum.photos/id/" . mt_rand(12, 250) . "/300/160")
                    ->setStock(mt_rand(10, 100))
                    ->setCategorie($categorie);
            
            $manager->persist($produit); // Ici on demande a manager de sauvegarder en cache , les produits que l'on creer a chaque tour
            }
        }

        $manager->flush(); // Ici on demande a manger de balancer toutes ces donnes (produits precedement crees) en base de donnees.

        // Pour lancer remplir la BDD avec les fixtures => doctrine:fixtures:load
    }

    //php bin/console d:f:l => yes
}
