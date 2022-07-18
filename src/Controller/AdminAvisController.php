<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisFormType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAvisController extends AbstractController
{
    /**
     * @Route("/admin/avis", name="app_admin_avis")
     */
    public function index(AvisRepository $avisRepo): Response
    {
        $avis = $avisRepo->findAll();

        return $this->render('admin_avis/index.html.twig', [
            'avis' => $avis
        ]);
    }

    /**
     * @Route("/admin/avis/detail/{id}", name="app_admin_avis_detail")
     */
    public function avisDetail($id, AvisRepository $avisRepo): Response
    {
        $avis = $avisRepo->find($id);

        return $this->render("admin_avis/detail.html.twig", [
            'avis' => $avis
        ]);
    }



    /**
     * @Route("/admin/avis/edit/{id}", name="app_admin_avis_edit")
     */
    public function avisEdit(Avis $avis = null, Request $request, EntityManagerInterface $manager):Response
    {
        if(!$avis)
        {
            $produit = new Avis;
        }
        $id = $produit->getId();

        if(!is_numeric($id) || is_null($id) )
        {
            $this->addFlash('warning', "Cette avis n'existe pas");

            return $this->redirectToRoute('app_admin_avis_edit');
        }

        //dd($avis);
        $form = $this->createForm(AvisFormType::class, $avis);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->flush();

            $this->addFlash('success', "L'avis a bien ete modifie");

            return $this->redirectToRoute("app_admin_avis");
        }

        return $this->render("admin_avis/edit.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/avis/del/{id}", name="app_admin_avis_del")
     */
    public function delAvis(Avis $avis, EntityManagerInterface $manager): Response
    {
        $manager->remove($avis);
        $manager->flush();

        $this->addFlash('danger', "L'avis "."a bien ete supprime");

        return $this->redirectToRoute("app_admin_avis");

    }

    

}

