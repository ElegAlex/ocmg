<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/09/18
 * Time: 16:39
 */

namespace AppBundle\Tableaux;

use CNAMTS\PHPK\CoreBundle\Generator\Table\Cell\CellLink;
use CNAMTS\PHPK\CoreBundle\Generator\Table\Decorator;
use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;


class ListeCiblageName extends TableGenerator
{

    /**
     * construction du tableau
     * 'name' correspond aux nom des champs du tableau
     * 'id' correspond au data des colonnes associées
     *
     * ListeCiblageName constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this
            ->addColumn(array(

                    'id' => 'id',
                    'decorator' => Decorator::LINK,
                    'filtrable' => false

                )
            )
            ->addColumn(array(

                    'id' => 'ciblageName',
                    'name' => 'Thèmes de ciblage',
                    'filtrable' => false

                )

            );

    }

    /**
     * retourne les lignes de data
     *
     * @return array|void
     * @throws \CNAMTS\PHPK\CoreBundle\Exception\PHPKCoreException
     * @throws \Exception
     *
     */
    public function getRows()
    {

        foreach ($this->getDataHandler()->getData() as $ciblageName) {

            $boutonSuppr = new CellLink(['path' => [
                    'route' => 'app_admin_deleteaccueil',
                    'param' => ['id' => $ciblageName['id']]
                ]
                ]
            );

            $boutonSuppr->setTitle('Supprimer');

            $boutonSuppr->setImage('fa fa-trash fa-2x');


            try {

                $this->addRow(

                    [

                        'class' => 'ciblage',

                        'data' => [

                            $boutonSuppr,

                            $ciblageName['ciblageName']

                        ],

                    ]

                );

            } catch (\Exception $e) {

                return ('une erreur est survenue' . $e->getMessage() . '. Le code erreur est' . $e->getCode());

            }

        }

        return $this->rows;
    }

}