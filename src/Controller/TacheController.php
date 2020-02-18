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
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $repository = $this->getDoctrine()->getRepository(Tache::class);
        $tache = $repository->findAll(['date_creation' => 'ASC']);
        return $this->render('tache/homes.html.twig',[
            "taches" => $tache
        ]);
    }
    /**
     * @Route("/trie", name="trie")
     */
    public function tacheTrier()
    {
        $repository = $this->getDoctrine()->getRepository(Tache::class);
        $tache = $repository->findBy(
            [],
            ['date_creation' => 'ASC']);
        return $this->render('tache/tache_trié.html.twig',[
            "taches" => $tache
        ]);
    }
    /**
     * @Route("/nouveau_perso", name="nouvelle_tache")
     * @Route("/modif/{id}", name="modif_tache" , methods = "GET|POST")
     */
    public function Ajout_modif_tache(Tache $tache = null, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        if(!$tache)
        {
            $tache = new Tache();
        }
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
