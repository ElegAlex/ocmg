<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 25/10/18
 * Time: 10:31
 */

namespace App\Tableaux;

use CNAMTS\PHPK\CoreBundle\Generator\Table\Cell\CellLink;
use CNAMTS\PHPK\CoreBundle\Generator\Table\Decorator;
use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;

class ListeRegionName extends TableGenerator
{

    /**
     * construction du tableau
     * 'name' correspond aux nom des champs du tableau
     * 'id' correspond au data des colonnes associées
     *
     * ListeRegionName constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();


        $this
            ->addColumn([

                    'id' => 'id',
                    'decorator' => Decorator::LINK,
                    'filtrable' => false


                ]
            )
            ->addColumn([

                    'id' => 'libelleRegion',
                    'name' => 'libelle de la region',
                    'filtrable' => true

                ]

            )
            ->addColumn([

                    'id' => 'isActive',
                    'decorator' => Decorator::BOOLEAN,
                    'decoratorOptions' => [

                        'booleans' => [

                            true => 'actif',
                            false => 'inactif'
                        ]

                    ],
                    'filtrable'=>false

                ]

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

        foreach ($this->getDataHandler()->getData() as $region) {

            $boutonSuppr = new CellLink(['path' => [
                    'route' => 'app_region_isactive',
                    'param' => ['id' => $region['id']]
                    ]
                ]
            );



            $boutonSuppr->setImage('fa fa-ban fa-2x');

            $boutonSuppr->setBackgroundColor($region['isActive'] === '1' ? CellLink::COLOR_VERT : CellLink::COLOR_ROUGE);

            try {

                $this->addRow(

                    [

                        'class' => 'region',

                        'data' => [

                            $boutonSuppr,

                            $region['libelleRegion'],

                            $region['isActive']

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