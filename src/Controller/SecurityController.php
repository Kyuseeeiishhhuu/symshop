<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/inscription", name="app_inscription")
     */
    public function inscription(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $manager): Response
    {
        //On a creer un nouvel exemplaire de l'entite User afin de pouvoir remplir l'objet via le formulaire , puis l'inserer en BDD
        $user = new User;

        //on execute la methode createForm() fournit par la classe AbstractController

        //dd($request);

        $formInscription = $this->createForm(InscriptionFormType::class, $user);


        /**
         * handleRequest() : methode du formulaire (createForm()), qui permet au formulaire de se gorger des informations qui sont ont ete transmises via request
         */
        $formInscription->handleRequest($request);

        if($formInscription->isSubmitted() && $formInscription->isValid())
        {
            // SI le formulaire a ette correctement rempli , bien valider (est-ce que chaque donnee saissie a bien ete transmis au bon setter), alors on peut faire le traitement 

            $passwordHash = $encoder->hashPassword($user, $user->getPassword());

            dump($passwordHash);

            //On ecrase son mdp saisie par le mdp hashe
            $user->setPassword($passwordHash);

            $user->setRoles(["ROLE_USER"]);

            dump($user);

            //sauvegarder en cache les donnes 

            $manager->persist($user);

            //On insert en BDD
            $manager->flush();

            //Fonction addFlash() permet d'enregistre un message en session 
            $this->addFlash("success", "Felicitation , votre compte est creer vous pouvez des a present vous connecter");


            //Une fois inscrit , on redirige l'utilisateur vers la page de connexion

            return $this->redirectToRoute("app_login");


        }

        //dd($user);

        return $this->render("security/inscription.html.twig", [
            'form' => $formInscription->createView()
        ]);
    }



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
