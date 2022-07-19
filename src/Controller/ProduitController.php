<?php

namespace App\Controller;

use DateTime;
use App\Entity\Avis;
use App\Form\AvisFormType;
use App\Repository\AvisRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function detail($id, ProduitRepository $produitRepo, Request $request, EntityManagerInterface $manager): Response
    {
        /*Autre possibiliter sans passer pzr l'injection de dependance
        $repo = $this->getDoctrine()->getRepository(Produit::class);

        $produit2= $repo->find($id);*/

        $avis= new Avis;

        $form = $this->createForm(AvisFormType::class, $avis);

        $form->handleRequest($request);

        dump($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $avis->setCreatedAt(new \DateTime())
            ->setProduit($produitRepo->find($id));

            $manager->persist($avis);
            $manager->flush();

            $this->addFlash('success', "Votre avis a bien ete poste!");

            return $this->redirectToRoute("app_detail_produits", [
                'id' => $id
            ]);
        }

        return $this->render("produit/detail.html.twig", [
            'produit' => $produitRepo->find($id),
            'form' =>$form->createView()
        ]);
    }

    /**
     * Fonction Affichage selon categorie***********
     * @Route("/categories/", name="app_categories")
     */
    public function categoriesAll(CategorieRepository $catRepos): Response
    {
        $categories = $catRepos->findAll();

        return $this->render("produit/categories.html.twig", [
            'categories' => $categories
        ]);
    }

     /**
     * Fonction Affichage selon categorie***********
     * @Route("/categorie/{id}", name="app_categorie_produits")
     */
    public function categorieProduit($id, CategorieRepository $catRepos): Response
    {
        $categorie = $catRepos->find($id);

        if(!$categorie)
        {
            $this->addFlash('warning', "Cette categorie n'existe pas");

            return $this->redirectToRoute('app_categories');
        }

        return $this->render("produit/categorie_produit.html.twig", [
            'categorie' => $categorie
        ]);
    }

}
