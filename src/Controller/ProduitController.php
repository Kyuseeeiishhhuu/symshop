<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*on cree un controller pour traiter une partie de notre application , ici en l'occurence la partie produit */

/* Tous les controllers heritent par defaut d'une classe contenant plusieurs methodes pour nous faciliter le traitement , c'est la classe abstraite AbstractController*/
class ProduitController extends AbstractController
{
    /**
     * Cette fonction conduit vers la page listant tous nos produits.
     * 
     * Le controlleur utilise les Routes (URL) comme des ecouteur et lorsqu'une route est appeler , une fonction correspondante a cette meme route se declenche 
     * 
     * @Route("/produits", name="app_produits")
     */
    public function index(ProduitRepository $produitRepo): Response
    {
        /*Pour selectionner des donnes dans une table SQL , nous avons le Respository de l'entite correspondante (Produit => 'ProduitRepository'). Le repository est une classe generee par doctrine , qui permet de faire des solections en BDD (SELECT). Pour cela, elle dispose de differentes methodes : find() , findAll() , findByOne() , findBy() .*/

        /*Pour pouvoir utiliser le ProduitRepository , on a la possibilite de l'injecter en dependance , c'est a dire , de l'entre en argument de la fonction. l'ArgumentResolver de symfony se chargera de m'instancier */

        $produits = $produitRepo->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits
        ]);
    }

    /** 
     * @Route("/produits/{id}", name="app_detail_produits" , requirements={"id"="\d+"})
     */
    public function detail($id, ProduitRepository $produitRepo): Response
    {
        /*Autre possibiliter sans passer pzr l'injection de dependance
        $repo = $this->getDoctrine()->getRepository(Produit::class);

        $produit2= $repo->find($id);*/

        return $this->render("produit/detail.html.twig", [
            'produit' => $produitRepo->find($id)
        ]);
    }

}
