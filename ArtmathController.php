<?php

/**************************************************************
 * Site symfony : Art Mathématique - courbe de koch           *
 **************************************************************
 * (c) F. BONNARDOT, 28 Février 2022                          *
 * This code is given as is without warranty of any kind.     *
 * In no event shall the authors or copyright holder be liable*
 *    for any claim damages or other liability.               *
 **************************************************************/

namespace App\Controller;

// Inclus par défaut par Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Récupération des données d'un formulaire
use Symfony\Component\HttpFoundation\Request;

// Exécution d'un process (ici fonction python)
// Doc : https://symfony.com/doc/current/components/process.html
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

// Pour renvoyer un fichier directement
use Symfony\Component\HttpFoundation\File\File;


class ArtmathController extends AbstractController
{
    /**
     * @Route("/", name="racine")
     */
    public function racine() : Response
    {
        // Redirige vers /artmath si on va sur le site sans
        //  indiquer le nom de la route
        return $this->redirectToRoute('app_artmath');
    }

    /**
     * @Route("/artmath", name="app_artmath")
     */
    public function index(): Response
    {
        return $this->render('artmath/index.html.twig', [
            'fichier' => '',
        ]);
    }

    /**
     * @Route("/calculer", name="calculer")
     */
    public function calculer(Request $request): Response
    {
        // Récupère les paramètres issus du formulaire (on indique le champ name)
        $amplitude_rota = $request -> request -> get("amplitude") ;
        $amplitude = $request -> request -> get("amplitude_rota") ;
        $nbcolonnes = $request -> request -> get("nbcolonnes") ;
        $nblignes = $request -> request -> get("nblignes") ;
        // Pour les boutons : si appui contenu champ value sinon NULL
        $calculer  = $request -> request -> get("calculer");
        $imprimer  = $request -> request -> get("imprimer");


        // Oui : Appelle le script Python koch.py qui se trouve dans le répertoire /public
        $process = new Process(['python3','nees_carre.py',$amplitude,$amplitude_rota,$nbcolonnes,$nblignes]);
        $process -> run();
        // Récupère la valeur de retour renvoyé par le script python
        $fichier="reponse.png"
        // Retourne un message si l'éxécution c'est mal passée
        if (!$process->isSuccessful())
            return new Response ("Erreur lors de l'éxécution du script Python :<br>".$process->getErrorOutput());    

        // A t'on appuyé sur calculer ?
        if ($calculer!=NULL)
            return $this->render('artmath/index.html.twig', [
                'fichier' => $fichier,
            ]);
        else {
            // On a appuyé sur imprimer
            return $this->render('artmath/imprimer.html.twig', [
                'fichier' => $fichier,
            ]);
        }
    }
}
