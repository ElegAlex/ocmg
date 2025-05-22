<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 24/10/18
 * Time: 10:18
 */

namespace AppBundle\Controller\AdministrationController;


use AppBundle\Entity\Region;
use AppBundle\Tableaux\ListeRegionName;
use CNAMTS\PHPK\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class AdminRegionController
 * @package AppBundle\Controller\AdministrationController
 */
class AdminRegionController extends AbstractController
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function showAction(Request $request)
    {


        $tableau = $this->get('phpk_core.tableau')->get(new ListeRegionName());

        // récupère les datas de la méthode liste du repository
        $tableau->getDataHandler()->setRepository($this->getRepository('AppBundle:Region'));
        $regionEtUtaaActives = $this->getRepository(Region::class)->departementsEtUtaaRegionActive();

        return $this->render(

            'AppBundle:Admin/Parametrage:parametrage.html.twig',

            array(

                'listeRegionName' => $tableau,
                'regionEtUtaaActives' => $regionEtUtaaActives

            )
        );
    }

    /**
     * Méthode qui permet d'activer et désactiver la région
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function isregionactiveAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $isActive = $this->getRepository(Region::class)->findBy(['is_active' => 1]);


        if ($isActive === []) {

            $inactive = $this->getRepository(Region::class)->find($id);

            $inactive->setIsActive(1);


        } else {

            $idActive = $this->getRepository(Region::class)->find($isActive[0]);


            $idActive->setIsActive(false);
            $inactive = $this->getRepository(Region::class)->find($id);

            $inactive->setIsActive(1);
            $em->persist($idActive);

        }

        $em->persist($inactive);
        $em->flush();
        $this->addFlash('success', 'la région a été activée avec succès');

        return $this->redirectToRoute('app_region_show');


    }

}