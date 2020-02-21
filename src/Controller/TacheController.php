<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TacheController extends AbstractController
{
    //page principale
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $repository = $this->getDoctrine()->getRepository(Tache::class);//on recupère tous les éléments de la base de donnée
        $tache = $repository->findby([],['date_creation' => 'ASC']);//on met par default le l'ordre en croissant
        return $this->render('tache/homes.html.twig',[
            "taches" => $tache,
            "option" => $filtre = null,//aucun filtre par default
            "option_date" =>  $trie = 'ASC'//on met par default le l'ordre croissant selectioner
        ]);
    }
    //route pour trier les taches
    /**
     * @Route("/trie", name="trie")
     */
    public function tacheTrier(Request $request, $trie = 'ASC', $filtre = null )
    {
        $repository = $this->getDoctrine()->getRepository(Tache::class);
        $filtre = $request->get('filtre');//on recupère la valeur filtre envoyer par le POST 
        $trie = $request->get('ordre');//on recupère la valeur ordre envoyer par le POST
        if($filtre === null)//Si il n'y pas de filtre alors
        {
            $tache = $repository->findBy(
                [],//sans filtre
                ['date_creation' => $trie]//on filre par date de création ASC
            );
        }
        else//si il y a des filtres
        {
            if($trie == 'ASC')
            {
                $tache = $repository->findBy(
                    ['statut' => $filtre],//on filtre par le statut
                    ['date_creation' => 'ASC']//et on met dans l'ordre croissant les taches par leur dates de création
                );
            }
            else
            {
                $tache = $repository->findBy(
                    ['statut' => $filtre],//on filtre par le statut
                    ['date_creation' => 'DESC']//et on met dans l'ordre décroissant les taches par leur dates de création
                );
            }
            // $tache = $repository->findBy(
            //     ['statut' => $filtre],
            //     ['date_creation' => $trie]/* Quand la variable $trie est utiliser pour choisir l'ordre de trie j'ai ce message d'erreur:
            //      'Invalid order by orientation specified for App\Entity\Tache#date_creation' cependant quand je met la valeur 'DESC' ou 'ASC'
            //       à la main dans le code cela fonctionne*/

            // );
        }
        return $this->render('tache/homes.html.twig',[//on applique les filtres et les tries dans la page
            "taches" => $tache,
            "option" => $filtre,//variable qui met l'élement selectioné précédement en selected
            "option_date" =>  $trie//variable qui met l'élement selectioné précédement en selected
        ]);
    }
    /*
    Route pour modifier et supprimer une tache
    */
    /**
     * @Route("/nouvelle_tache", name="nouvelle_tache")
     * @Route("/modif/{id}", name="modif_tache" , methods = "GET|POST")
     */
    public function Ajout_modif_tache(Tache $tache = null, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if(!$tache)//Si la tache est null alors on en crée une 
        {
            $tache = new Tache();
            $tache->setDateCreation(new \DateTime('now'));//on prérempli la date et l'heure pour qu'elle soit corresponde à l'actuelle
        }
        $tache->setMiseAJour(new \DateTime('now'));// dans le cas de la modification et de la création d'une tache on met une date de mise a jours actuelle
        $form = $this->createForm(TacheType::class,$tache);// on envoie le formulaire
        $form->handleRequest($request);// on récupere la requète
        if($form->isSubmitted() && $form->isValid())//si le form est valider
        {
            $modif = $tache->getId() !== null;
            $entityManagerInterface->persist($tache);// on rentre toute les infos
            $entityManagerInterface->flush();//on met à jours la base de donnée
            $this->addFlash("success", ($modif) ?"La modification a été effectuée" : "Le nouveau Tache a été créé");//un message est affiché pour confirmer la modification la création de la tahce
            return $this->redirectToRoute("home");//on redirige vers la page principale
        }
        return $this->render('tache/tacheFormulaire.html.twig', [// si il n'y pas de requete valider on envoie le formulaire avec la nouvelle tache au formulaire ou la tache modifier dans la page du formulaire
            "tache" => $tache,
            "form" => $form->createView(),
            "ismodification" => $tache->getId() !== null
        ]);
    }
    /*
    Route pour suprimer une tache
    */
    /**
     * @Route("/{id}", name="suppression", methods = "delete")
     */
    public function suppr_tache(Tache $tache, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if($this->isCsrfTokenValid("SUP". $tache->getId(), $request->get('_token')))//On verifie si le code envoyer par la page twig est identique pour eviter qu'un utilisateur face une suppression non désirer
        {
            $entityManagerInterface->remove($tache);
            $entityManagerInterface->flush();//On supprime et on actualise la base de donnée
            $this->addFlash("success", "La suppression a été effectuée");//un message est affiché pour confirmer la suppression
            return $this->redirectToRoute("home");
        }
        
    }
}
