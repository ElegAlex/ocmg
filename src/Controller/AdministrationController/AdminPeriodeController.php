<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 17/10/18
 * Time: 14:53
 */

namespace App\Controller\AdministrationController;


use App\Entity\Periode;
use App\Form\ajoutPeriodeType;
use App\Tableaux\ListePeriodeName;
use CNAMTS\PHPK\CoreBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminPeriodeController
 * @package App\Controller\AdministrationController
 */
class AdminPeriodeController extends AbstractController
{

    /**
     * ajout au paramétrage de l'application de la periode ciblée :
     * l'ajout d'une nouvelle période annule la précédente
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function ajoutperiodeAction(Request $request)
    {


        $periode = new Periode();

        $form = $this->createForm(

            ajoutPeriodeType::class,

            $periode

        );

        // gestion de la soumission du formulaire
        $form->handleRequest($request);

        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires
        if ($form->isSubmitted() && $form->isValid()) {

            try {

                $em = $this->getDoctrine()->getManager();


                $existingPeriode = $this->getRepository(Periode::class)
                    ->findOneBy(['annee' => $form->getNormData()->getAnnee(),

                        'is_semestre' => $form->getNormData()->isSemestre()]);

                $existingPeriodeActive = $em->getRepository(Periode::class)->findBy(['is_active' => true]);

                if (($existingPeriode !== null) && ($existingPeriodeActive !== [])) {

                    $existingPeriodeActive[0]->setIsActive(false);
                    $existingPeriode->setIsActive(true);
                    $em->persist($existingPeriodeActive[0]);
                    $em->persist($existingPeriode);
                }

                if (($existingPeriode === null) && ($existingPeriodeActive !== [])) {

                    if ($form->getViewData()->isSemestre() === false) {
                        $existingPeriodeActive[0]->setIsSemestre(false);
                        $existingPeriodeActive[0]->setAnnee($form->getViewData()->getAnnee());
                        $existingPeriodeActive[0]->setCode($form->getViewData()->getAnnee());
                    } else {
                        $existingPeriodeActive[0]->setIsSemestre(true);
                        $existingPeriodeActive[0]->setAnnee($form->getViewData()->getAnnee());
                        $existingPeriodeActive[0]->setCode($form->getViewData()->getAnnee() . '_S1');
                    }

                }
                if (($existingPeriode === null) && ($existingPeriodeActive === [])) {

                    $periode->setAnnee($form->getViewData()->getAnnee());
                    $periode->setIsActive(true);
                    $periode->setIsSemestre($form->getViewData()->isSemestre());

                    if ($form->getViewData()->isSemestre() === true) {

                        $periode->setCode($form->getViewData()->getAnnee() . '_S1');

                    } else {

                        $periode->setCode($form->getViewData()->getAnnee());

                    }

                    $em->persist($periode);


                }

                $em->flush();


                $this->notification(
                    'la periode a bien été enregistrée ');

                return $this->redirect(

                    $this->generateUrl(

                        'app_periode_ajouter'

                    )

                );

            } catch (\Exception $e) {

                // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                $this->error('une erreur est survenue' . $e->getMessage());

            }

        }

        if ($this->getRepository(Periode::class)->findAll() !== []) {

            $tableau = $this->get('phpk_core.tableau')->get(new ListePeriodeName());

            // récupère les datas de la méthode liste du repository
            $tableau->getDataHandler()->setData($this->getRepository(Periode::class)->findBy(['is_active' => 1]));

            return $this->render(

                'Admin/Parametrage/parametrage.html.twig',

                [

                    'formPeriode' => $form->createView(),

                    'listePeriodeName' => $tableau

                ]
            );
        } else {

            return $this->render(

                'Admin/Parametrage/parametrage.html.twig',

                [

                    'formPeriode' => $form->createView()

                ]

            );

        }

    }

}