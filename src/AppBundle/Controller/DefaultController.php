<?php

namespace AppBundle\Controller;


use AppBundle\Tools\Download;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends AbstractController {


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        $themes = $this->getRepository('AppBundle:Ciblage')->findAll();
        $textAccueil = $this->getRepository('AppBundle:TextAccueil')->findAll();
        
        if (isset($textAccueil[0])) {

            $texte = strip_tags($textAccueil[0]->getTextAccueil(), ENT_HTML5);

            return $this->render('AppBundle:Default:index.html.twig',

                [

                    'textAccueil' => $textAccueil,
                    'texte' => $texte,
                    'ciblage' => $themes

                ]
            );
        } else {

            return $this->render('AppBundle:Default:index.html.twig',
                [

                    'textAccueil' => $textAccueil,
                    'ciblage' => $themes

                ]
            );

        }

    }


    /**
     *  méthode permettant le chargement du fichier de l'aide en ligne administrateur
     */

    public function downloadDocAdminAction(){

        return Download::downloadAction(

            $this->get('kernel')->getRootDir().'/../download/','doc_admin.pdf'

        );

    }

    /**
     *  méthode permettant le chargement du fichier de l'aide en ligne utilisateur
     */

    public function downloadDocUserAction(){

        return Download::downloadAction(

            $this->get('kernel')->getRootDir().'/../Download/','doc_utilisateur.doc'

        );

    }
}
