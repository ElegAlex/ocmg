<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 31/08/18
 * Time: 10:23
 */

namespace App\Tableaux;


use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;
use Symfony\Component\HttpFoundation\Session\Session;

class ListeCiblageUpload extends TableGenerator
{

    /**
     * construction du tableau
     * 'name' correspond aux noms des champs du tableau
     * 'id' correspond au data des colonnes associées
     *
     * ListeCiblageUpload constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $session = new Session();
        if (count($session->get('colonneTheme')) >= 20) {

            foreach ($session->get('colonneTheme') as $value) {

                $this
                    ->addColumn([

                            'id' => $value,
                            'name' => $value,
                            'width' => '10'

                        ]

                    );
            }
        } else {

            foreach ($session->get('colonneTheme') as $value) {

                $this
                    ->addColumn([

                            'id' => $value,
                            'name' => $value,
                        ]
                    );
            }

        }
    }


    /**
     *
     * retourne les lignes de data
     *
     * @return array|void
     * @throws \CNAMTS\PHPK\CoreBundle\Exception\PHPKCoreException
     * @throws \Exception
     */
    public function getRows()
    {
        $session = new Session();

        foreach ($this->getDataHandler()->getData() as $listeCiblageUpload) {

            $listRows = [];

            foreach ($session->get('colonneTheme') as $keyData) {

                $listRows[] = [$listeCiblageUpload[$keyData]];

            }

            try {

                $this->addRow(

                    [

                        'data' => $listRows

                    ]

                );

            } catch (\Exception $e) {

                return ('une erreur est survenue' . $e->getMessage() . '. Le code erreur est' . $e->getCode());

            }

        }

        return $this->rows;
    }

}