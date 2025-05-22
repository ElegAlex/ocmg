<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/12/18
 * Time: 11:17
 */

namespace AppBundle\Controller\AdministrationController;


use AppBundle\Controller\AbstractController;
use AppBundle\Entity\TextAccueil;
use AppBundle\Form\textAccueilType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminTextAccueilController
 * @package AppBundle\Controller\AdministrationController
 */
class AdminTextAccueilController extends AbstractController
{
    /**
     * ajout et modification du titre et du texte d'accueil
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajoutTextAccueilAction(Request $request)
    {

        $text = $this->getRepository(TextAccueil::class)->findAll();

        //si aucun texte d'accueil existe
        if ($text === []) {

            $textAccueil = new TextAccueil();
            $form = $this
                ->createForm(
                    textAccueilType::class,
                    $textAccueil
                );

        } else {
            //si il existe déjà un texte d'accueil
            $textAccueil = $text[0];

            $form = $this
                ->createForm(
                    textAccueilType::class,
                    $textAccueil
                );

        }

        // gestion de la soumission du formulaire
        $form->handleRequest($request);

        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires
        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $em = $this->getDoctrine()->getManager();

                // Persistance de l'entité textAccueil nouvellement créée
                $em->persist($textAccueil);

                // Flush des modifications pour que l'insertion du nom du texteAccueil se fasse en base
                $em->flush();

                $this->notification(
                    'le titre et le texte de la page d\'accueil ont bien été enregistrés ');

                return $this->redirect(
                    $this->generateUrl(

                        'app_index'

                    )
                );
            } catch (\Exception $e) {

                // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                $this->error('une erreur est survenue' . $e->getMessage());

            }
        }


        return $this->render(

            'AppBundle:Admin/Parametrage:parametrage.html.twig',

            array(

                'formText' => $form->createView(),
                $textAccueil


            )
        );
    }

}