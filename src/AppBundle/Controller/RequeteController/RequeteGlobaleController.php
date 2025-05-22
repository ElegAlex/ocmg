<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 01/10/18
 * Time: 09:19
 */

namespace AppBundle\Controller\RequeteController;


use AppBundle\Entity\Age;
use AppBundle\Entity\Data;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Theme;
use AppBundle\Entity\Utaa;
use AppBundle\Entity\Periode;

use AppBundle\Form\dataType;
use AppBundle\Tableaux\ListeRequeteGlobale;

use CNAMTS\PHPK\CoreBundle\Controller\AbstractController;
use CNAMTS\PHPK\CoreBundle\Form\Type\CollectionButtonType;
use CNAMTS\PHPK\CoreBundle\Generator\Form\Bouton;
use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 *
 *
 * Class RequeteGlobaleController
 * @package AppBundle\Controller\RequeteController
 */
class RequeteGlobaleController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function requeteGlobaleAction(Request $request)
    {

        $form = $this
            ->createForm(dataType::class

            )->add(

                'form1',
                CollectionButtonType::class,

                array(

                    'collection' => array(


                        'Valider le formulaire' => array(

                            'predefined' => Bouton::PREDEFINED_VALIDER,

                        ),
                    ),
                )
            )->add(

                'suppr',
                CollectionButtonType::class,

                array(

                    'collection' => array(


                        'init le formulaire' => array(

                            'predefined' => Bouton::PREDEFINED_EFFACER,
                            'url' => '/requete/globale',

                        ),
                    ),
                )
            );
        $periodeEnCours = $this->getDoctrine()->getManager()->getRepository(Periode::class)->findBy(['is_active' => 1]);
        // gestion de la soumission du formulaire
        $form->handleRequest($request);

        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                //nouvelle session
                $session = new Session();

                //recupère la période
                $periode = $periodeEnCours[0]->getCode();

                if (isset($_POST['appbundle_requeteglobale']['theme']) && count($_POST['appbundle_requeteglobale']['theme']) >= 2) {

                    if (isset($_POST['appbundle_requeteglobale']['praticien']['utaa']['code_utaa'])
                        && $_POST['appbundle_requeteglobale']['praticien']['utaa']['code_utaa'] !== '') {

                        $utaaId = $_POST['appbundle_requeteglobale']['praticien']['utaa']['code_utaa'];

                    } else {

                        $utaaId = '0';

                    }

                    //récupere l'ageId
                    if (isset($_POST['appbundle_requeteglobale']['age'])) {

                        $ageId = $_POST['appbundle_requeteglobale']['age']['code_age'];

                    } else {

                        $ageId = '';
                    }

                    if ($ageId === '') $ageId = '0';

                    //recupère l id de la période
                    $periodeId = $periodeEnCours[0]->getId();

                    //recupère id de theme
                    $themeId = $_POST['appbundle_requeteglobale']['theme']['theme'];

                    //recupère le tableau de commentaires et mise en session
                    $commentaire = array_diff_key(
                        $_POST['appbundle_requeteglobale']['theme'],
                        ['theme' => 0]);

                    $session->set('commentaire', $commentaire);

                    //récupère l'id du departement
                    if ($_POST['appbundle_requeteglobale']['praticien']['utaa']['departement']) {

                        $depId = $_POST['appbundle_requeteglobale']['praticien']['utaa']['departement'];

                    } else {

                        $depId = '';


                    }
                    if ($depId === '') $depId = '0';

                    return $this->redirectToRoute('app_requete_globale_resultat'

                        , [
                            'periodeId' => $periodeId,
                            'periode' => $periode,
                            'themeId' => $themeId,
                            'depId' => $depId,
                            'utaaId' => $utaaId,
                            'ageId' => $ageId
                        ]
                    );

                } else {

                    $this->notification('Vous devez sélectionner les champs avant de valider ', 'information');

                }


            } catch (\Exception $e) {

                // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                $this->error('une erreur est survenue' . $e->getMessage());

            }

        }


        //rendu du formulaire préparant la requete globale
        return $this->render(

            'AppBundle:Requete:globale.html.twig',

            [

                'formRequeteGlobale' => $form->createView(),
                'periodeEnCours' => $periodeEnCours[0]->getCode()


            ]
        );

    }


    /**
     * affiche le tableau de résultats de la requete globale
     *
     * @param Request $request
     * @return Response
     */
    public function resultatAction(Request $request)
    {

        $session = new Session();

        //si l'ageId a une valeur 0
        if ($request->get('ageId') === '0') {
            $ageId = '';
        } else {
            $ageId = $request->get('ageId');
        }

        //si l'utaaId a une valeur 0
        if ($request->get('utaaId') === '0') {
            $utaaId = '';
        } else {
            $utaaId = $request->get('utaaId');
        }

        //si l'id du département a une valeur 0
        if ($request->get('depId') === '0') {
            $depId = '';
        } else {
            $depId = $request->get('depId');
        }


        //recupere les clés=>values corespondant à toutes les datas de la requete
        $datasAssoc = $this->getRepository(Data::class)
            ->selectDatasRequeteGlobale(
                $request->get('themeId'),
                $ageId,
                $request->get('periodeId'),
                $session->get('commentaire'),
                $depId,
                $utaaId);

        //libelles qui constituent les champs du tableau listerequeteglobale
        $session->set('commentAssoc', []);
        $commentAssoc = array_keys($datasAssoc[0]);
        $session->set('commentAssoc', $commentAssoc);


        /**
         * @var $tableau TableGenerator
         */
        $tableau = $this->get('phpk_core.tableau')->get(new ListeRequeteGlobale());

        $session->set('tableau', []);

        // set les datas dans le DataHandler
        $tableau
            ->getDataHandler()
            ->setData($datasAssoc)
            ->setPageSize(20)
            ->setUseOutputWalkers(true)
            ->setFiltered(true);

        $session->set('tableau', $tableau);

        //recherche du ou des departements selectionnés
        $departement = $this->getRepository(Departement::class)->findById($request->get('depId'));

        //récupération du libellé du département ainsi que du code département
        if (count($departement) === 1) {
            $libelleDep = $departement[0]->getLibelleDep();
            $codeDep = $departement[0]->getCodeDep();
        } else {
            $libelleDep = 'les départements';
            $codeDep = 'Tous';
        }

        //recherche du theme selectionné
        $theme = $this->getRepository(Theme::class)->findById($request->get('themeId'));
        //recupération du libelle du theme
        $libelleTheme = $theme[0]->getLibelleTheme();


        //recherche de l'utaa selectionnee
        $utaa = $this->getRepository(Utaa::class)->findById($request->get('utaaId'));

        //récupération du numéro de l'utaa
        if (count($utaa) === 1) {
            $codeUtaa = $utaa[0]->getCodeUtaa();
        } else {
            $codeUtaa = 'Toutes';
        }

        //recherche de l'entité corresponadant à l'ageId sélectionné
        $age = $this->getRepository(Age::class)->findById($ageId);

        //récupération de la tranche d'âge si elle existe
        if (count($age) === 1) {
            $age = $age[0]->getCodeAge();
        } else {
            $age = '0';
        }

        return $this->render(

            'AppBundle:Requete:resultat.html.twig',
            [
                'listeRequeteGlobale' => $tableau,
                'periode' => $request->get('periode'),
                'theme' => $libelleTheme,
                'codeDep' => $codeDep,
                'codeUtaa' => $codeUtaa,
                'libelleDep' => $libelleDep,
                'age' => $age,

            ]
        );
    }

    /**
     * gère l'export des datas au format .csv
     *
     * @param Request $request
     *
     * @return \CNAMTS\PHPK\ExportBundle\DependencyInjection\Service\Response
     * @throws \CNAMTS\PHPK\ExportBundle\Exception\PHPKExportException
     */
    public function exportAction(Request $request)
    {

        $session = new Session();

        $export = $this->container->get('phpk_export.csv');

        //depot des datas mises en session dans requeteGlobaleAction()
        $export->setTableau($session->get('tableau'));

        if ($request->get('age') !== 0) {

            //nom du fichier enrichi des paramètres de la requête
            $export->setFilename('listeRequeteGlobale' . $request->get('periode') . '
                    ' . $request->get('theme') . ' ' . $request->get('dep') . '
                    ' . $request->get('utaa') . ' ' . $request->get('age'));


            //titre du fichier enrichi des paramères de la requête
            $export->setTitle('listeRequeteGlobale ' . $request->get('periode') . '
                    ' . $request->get('theme') . ' ' . $request->get('dep') . '
                    ' . $request->get('utaa') . ' ' . $request->get('age'));
        } else {


            //nom du fichier enrichi des paramètres de la requête
            $export->setFilename('listeRequeteGlobale' . $request->get('periode') . '
                    ' . $request->get('theme') . ' ' . $request->get('dep') . '
                    ' . $request->get('utaa'));


            //titre du fichier enrichi des paramères de la requête
            $export->setTitle('listeRequeteGlobale ' . $request->get('periode') . '
                    ' . $request->get('theme') . ' ' . $request->get('dep') . '
                    ' . $request->get('utaa'));


        }
        return $export->output();


    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
    */
    public function ajaxListUtaaByDepartementAction( Request $request)
    {

        $departementId = $request->get('departementId');

        $listUtaas=  $this->getDoctrine()->getManager()
            ->getRepository(Utaa::class)
            ->listUtaasByDepartement($departementId);


            $response =  new Response();

            $data = json_encode($listUtaas);

            $response->headers->set('Content-Type', 'application/json');

            $response->setContent($data);

            return $response;

       // return new Response("Erreur: ce n'est pas une requete ajax", 400);

    }

}


