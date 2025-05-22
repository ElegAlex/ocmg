<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/10/18
 * Time: 08:46
 */

namespace App\Tableaux;

use CNAMTS\PHPK\CoreBundle\Generator\Table\Decorator;
use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;


class ListePeriodeName extends TableGenerator
{

    /**
     * construction du tableau
     * 'name' correspond aux nom des champs du tableau
     * 'id' correspond au data des colonnes associées
     *
     * ListePeriodeName constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();


        $this
            ->addColumn([

                    'id' => 'code',
                    'name' => 'Période',
                    'filtrable' => false

                ]

            )
            ->addColumn([

                    'id' => 'isActive',
                    'name' => 'Statut',
                    'decorator' => Decorator::BOOLEAN,
                    'decoratorOptions' => [

                        'booleans' => [

                            true => 'actif',
                            false => 'inactif'
                        ]

                    ]

                ]

            );

    }

    /**
     * retourne les lignes de data Periodes actives
     *
     * @return array|void
     * @throws \CNAMTS\PHPK\CoreBundle\Exception\PHPKCoreException
     * @throws \Exception
     *
     */
    public function getRows()
    {

        foreach ($this->getDataHandler()->getData() as $periode) {

            try {

                $this->addRow(

                    [

                        'class' => 'periode',

                        'data' => [

                            $periode->getCode(),

                            $periode->Isactive(),

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