<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TacheController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $repository = $this->getDoctrine()->getRepository(Tache::class);
        $tache = $repository->findby([],['date_creation' => 'ASC']);
        return $this->render('tache/homes.html.twig',[
            "taches" => $tache,
            "option" => $filtre = null,
            "option_date" =>  $trie = 'ASC'
        ]);
    }
    /**
     * @Route("/trie", name="trie")
     */
    public function tacheTrier(Request $request, $trie = null, $filtre = null )
    {
        $repository = $this->getDoctrine()->getRepository(Tache::class);
        $filtre = $request->get('filtre');
        $trie = $request->get('ordre');
        if($filtre === null)
        {
            $tache = $repository->findBy(
                [],
                ['date_creation' => $trie]
            );
        }
        else
        {
            if($trie == 'ASC')
            {
                $tache = $repository->findBy(
                    ['statut' => $filtre],
                    ['date_creation' => 'ASC']
                );
            }
            else
            {
                $tache = $repository->findBy(
                    ['statut' => $filtre],
                    ['date_creation' => 'DESC']
                );
            }
            // $tache = $repository->findBy(
            //     ['statut' => $filtre],
            //     ['date_creation' => $trie]/* Quand la variable $trie est utiliser pour choisir l'ordre de trie j'ai ce message d'erreur:
            //      'Invalid order by orientation specified for App\Entity\Tache#date_creation' cependant quand je met la valeur 'DESC' ou 'ASC'
            //       à la main dans le code cela fonctionne*/

            // );
        }
        return $this->render('tache/homes.html.twig',[
            "taches" => $tache,
            "option" => $filtre,
            "option_date" =>  $trie
        ]);
    }
    /**
     * @Route("/nouvelle_tache", name="nouvelle_tache")
     * @Route("/modif/{id}", name="modif_tache" , methods = "GET|POST")
     */
    public function Ajout_modif_tache(Tache $tache = null, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if(!$tache)
        {
            $tache = new Tache();
            $tache->setDateCreation(new \DateTime('now'));
        }
        $tache->setMiseAJour(new \DateTime('now'));
        $form = $this->createForm(TacheType::class,$tache);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $modif = $tache->getId() !== null;
            $entityManagerInterface->persist($tache);
            $entityManagerInterface->flush();
            $this->addFlash("success", ($modif) ?"La modification a été effectuée" : "Le nouveau Tache a été créé");
            return $this->redirectToRoute("home");
        }
        return $this->render('tache/tacheFormulaire.html.twig', [
            "tache" => $tache,
            "form" => $form->createView(),
            "ismodification" => $tache->getId() !== null
        ]);
    }
    /**
     * @Route("/{id}", name="suppression", methods = "delete")
     */
    public function suppr_personnages(Tache $tache, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if($this->isCsrfTokenValid("SUP". $tache->getId(), $request->get('_token')))
        {
            $entityManagerInterface->remove($tache);
            $entityManagerInterface->flush();
            $this->addFlash("success", "La suppression a été effectuée");
            return $this->redirectToRoute("home");
        }
        
    }
}
