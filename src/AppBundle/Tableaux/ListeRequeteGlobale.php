<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 01/10/18
 * Time: 13:33
 */

namespace AppBundle\Tableaux;


use CNAMTS\PHPK\CoreBundle\Generator\Table\Decorator;
use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;
use Symfony\Component\HttpFoundation\Session\Session;


class ListeRequeteGlobale extends TableGenerator
{

    public function __construct()
    {

        parent::__construct();

        $session = new Session();

        foreach ($session->get('commentAssoc') as $value) {

            $this
                ->addColumn([

                        'id' => $value,
                        'name' => $value,

                        'triable' => true,
                        'decorator' => Decorator::LONGTEXT,
                        'sort' => 'SORT_REGULAR'
                    ]
                );

        }

    }

    /**
     * retourne les lignes de data après requete
     *
     * @return array|void
     * @throws \CNAMTS\PHPK\CoreBundle\Exception\PHPKCoreException
     * @throws \Exception
     */
    public function getRows()
    {

        foreach ($this->getDataHandler()->getData() as $key => $resultatRequeteGlobale) {

            try {

                $this->addRow([

                    'data' => $resultatRequeteGlobale

                ]);

            } catch (\Exception $e) {

                return ('une erreur est survenue' . $e->getMessage() . '. Le code erreur est' . $e->getCode());

            }

        }


        return $this->rows;

    }
}