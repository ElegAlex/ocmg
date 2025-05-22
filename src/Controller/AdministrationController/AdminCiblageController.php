<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 17/09/18
 * Time: 10:02
 */

namespace App\Controller\AdministrationController;


use App\Controller\AbstractController;
use App\Entity\Ciblage;
use App\Form\ajoutThemeCiblageType;
use App\Tableaux\ListeCiblageName;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminCiblageController
 * @package App\Controller\AdministrationController
 */
class AdminCiblageController extends AbstractController
{

    /**
     * méthode qui permet d'ajouter un nom de  ciblage
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajoutThemeCiblageAction(Request $request)
    {
        $ciblage = new Ciblage();


        $form = $this->createForm(

            ajoutThemeCiblageType::class,

            $ciblage

        );

        // gestion de la soumission du formulaire
        $form->handleRequest($request);

        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires
        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em = $this->getDoctrine()->getManager();

                // Persistance de l'entité textAccueil nouvellement créée
                $em->persist($ciblage);

                // Flush des modifications pour que l'insertion du nom du texteAccueil se fasse en base
                $em->flush();

                $this->notification(
                    'le titre et le texte de la page d\'accueil ont bien été enregistrés ');

                return $this->redirect(

                    $this->generateUrl(

                        'app_admin_paramciblage'

                    )

                );

            } catch (\Exception $e) {

                // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                $this->error('une erreur est survenue' . $e->getMessage());

            }
        }

        $ciblage = $this->getRepository(Ciblage::class)->findAll();

        $tableau = $this->get('phpk_core.tableau')->get(new ListeCiblageName());

        // récupère les datas de la méthode liste du repository
        $tableau->getDataHandler()->setRepository($this->getRepository(Ciblage::class));

        return $this->render(

            'Admin/Parametrage/parametrage.html.twig',

            array(

                'formCiblage' => $form->createView(),

                'ciblage' => $ciblage,

                'listeCiblageName' => $tableau

            )
        );

    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {

        $em = $this->getDoctrine()->getManager();

        if ($id) {

            $delete = $this->getRepository(Ciblage::class)->find($id);

            $em->remove($delete);

            $em->flush();

            $this->addFlash('success', 'le thème de ciblage a été supprimé avec succès');

            return $this->redirectToRoute('app_admin_paramciblage');

        }

        //message personnalisé
        $this->addFlash('success', 'le texte d\'accueil  a été supprimé avec succès');

        return $this->redirectToRoute('app_index');
    }


}
