<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 30/08/18
 * Time: 09:51
 */

namespace App\Tableaux;


use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;

class ListePraticiensUpload extends TableGenerator
{

    /**
     *  construction du tableau
     * 'name' correspond aux nom des champs du tableau
     * 'id' correspond au data des colonnes associées
     *
     * ListePraticiensUpload constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this
            ->addColumn([

                'id' => 'nom_prat',
                'name' => 'Nom du praticien'

            ])
            ->addColumn([

                'id' => 'prenom_prat',
                'name' => 'Prénom du praticien'

            ])
            ->addColumn([

                'id' => 'code_praticien',
                'name' => 'numero praticien '

            ])
            ->addColumn([

                'id' => 'cle_prat',
                'name' => 'cle '

            ])
            ->addColumn([

                'id' => 'libelle_ville',
                'name' => 'ville '

            ])
            ->addColumn([

                'id' => 'code_utaa',
                'name' => 'utaa '

            ]);

    }

    /**
     * retourne les lignes de data
     *
     * @return array|void
     * @throws \CNAMTS\PHPK\CoreBundle\Exception\PHPKCoreException
     * @throws \Exception
     */
    public function getRows()
    {

        foreach ($this->getDataHandler()->getData() as $listePraticiensUpload) {

            try {

                $this->addRow(

                    [

                        'data' => [

                            $listePraticiensUpload['nom_prat'],
                            $listePraticiensUpload['prenom_prat'],
                            $listePraticiensUpload['code_praticien'],
                            $listePraticiensUpload['cle_prat'],
                            $listePraticiensUpload['libelle_ville'],
                            $listePraticiensUpload['code_utaa'],
                        ]

                    ]

                );

            } catch (\Exception $e) {

                return ('une erreur est survenue' . $e->getMessage() . '. Le code erreur est' . $e->getCode());

            }

        }

        return $this->rows;

    }


}