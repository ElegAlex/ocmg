<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 25/07/18
 * Time: 15:41
 */


namespace AppBundle\Tableaux;


use CNAMTS\PHPK\CoreBundle\Generator\Table\Decorator;
use CNAMTS\PHPK\CoreBundle\Generator\Table\TableGenerator;
use CNAMTS\PHPK\CoreBundle\Generator\Table\Cell\CellLink;


class ListeUpload extends TableGenerator
{

    public function __construct()
    {
        parent::__construct();


        $this
            ->addColumn([
                'id' => 'id',
                'decorator'=>Decorator::LINK,

            ])
            ->addColumn([
                'id' => 'filename',
                'name' => 'Ciblage '

            ])
            ->addColumn([
                'id' => 'updated_at',
                'name' => 'mis à jour le'

            ])
            ->addColumn([
                'id' => 'file_size',
                'name' => 'Taille du fichier '

            ])
            ->addColumn([
                'id' => 'path',
                'name' => 'chemin'

            ])
            ->addColumn([
                'id' => 'original_name',
                'name' => 'nom du fichier'

            ])
            ->addColumn([
                'id' => 'error_code',
                'name' => 'code statut'

            ])
            ->addColumn([
                'id' => 'nb_col_upload_file',
                'name' => 'nb col du fichier '

            ])
            ->addColumn([
                'id' => 'nb_row_upload_file',
                'name' => 'nb lignes du fichier '

            ])
            ->addColumn([
                'id' => 'nb_row_uploaded',
                'name' => 'nb lignes chargées'
            ])
            ->addColumn([
                'decorator' => Decorator::LOUPE,
                'visibility' => true

            ]);

    }


    /**
     * @return array|void
     * @throws \CNAMTS\PHPK\CoreBundle\Exception\PHPKCoreException
     * @throws \Exception
     */
    public function getRows()
    {

      if (count($this->getDataHandler()->getData()) !==1)
        {
            //si il y a des fichiers de themes chargés , blocage de la suppression du fichier praticiens.
            // Seuls les fichiers de  thèmes et commentaires peuvent être supprimés
            foreach ($this->getDataHandler()->getData() as $fichierUpload)

            {

                $boutonSuppr = new CellLink(['path' => [
                        'route' => 'app_import_fichier_delete',
                        'param' => ['id' => $fichierUpload['id']
                        ]
                    ]
                ]);

                $boutonSuppr->setTitle('Supprimer');

                $boutonSuppr->setImage('fa fa-trash fa-2x');


                $datefinvalidite = date(

                    'd/m/Y', strtotime(
                        "+6 months", strtotime(
                            $fichierUpload['updated_at']
                        )
                    )
                );
                $date = date('d/m/Y');

                if ($fichierUpload['filename'] === 'praticien') {
                    $boutonSuppr = new CellLink(['path' => [
                            'route' => 'app_import_fichier_liste',
                            'param' => ['id' => $fichierUpload['id']
                            ]
                        ]
                    ]);

                    $boutonSuppr->getDisabled();
                    $boutonSuppr->setImage('far fa-times-circle fa-2x');


                }


            try {

                $this->addRow(

                    [
                        'data' =>
                            [

                                $boutonSuppr,
                                $fichierUpload['filename'],
                                $fichierUpload['updated_at'],
                                $fichierUpload['file_size'],
                                $fichierUpload['path'],
                                $fichierUpload['original_name'],
                                $fichierUpload['error_code'],
                                $fichierUpload['nb_col_upload_file'],
                                $fichierUpload['nb_row_upload_file'],
                                $fichierUpload['nb_row_uploaded']

                            ],
                        'path' => [
                            'route' => $this->getRoute(),
                            'param' => ['id' => $fichierUpload['id'],

                            ]

                        ]
                    ]

                );


                } catch (\Exception $e) {

                    return ('une erreur est survenue' . $e->getMessage() . '. Le code erreur est' . $e->getCode());

                }
            }
            return $this->rows;

        }
        else
            {
//si il ne reste que le fichier des praticiens à afficher
                $fichierUpload = $this->getDataHandler()->getData();

                $boutonSuppr = new CellLink(['path' => [
                        'route' => 'app_import_fichier_delete',
                        'param' => ['id' => $fichierUpload[0]['id']]
                        ]
                    ]
                );

                $boutonSuppr->setTitle('Supprimer');

                $boutonSuppr->setImage('fa fa-trash fa-2x');

                $datefinvalidite = date(
                    'd/m/Y', strtotime(
                        "+6 months", strtotime(
                            $fichierUpload[0]['updated_at'])));
                $date = date('d/m/Y');



                try {

                    $this->addRow(

                        [
                            'data' =>
                                [

                                    $boutonSuppr,
                                    $fichierUpload[0]['filename'],
                                    $fichierUpload[0]['updated_at'],
                                    $fichierUpload[0]['file_size'],
                                    $fichierUpload[0]['path'],
                                    $fichierUpload[0]['original_name'],
                                    $fichierUpload[0]['error_code'],
                                    $fichierUpload[0]['nb_col_upload_file'],
                                    $fichierUpload[0]['nb_row_upload_file'],
                                    $fichierUpload[0]['nb_row_uploaded']
                                ],
                            'path' => [
                                'route' => $this->getRoute(),
                                'param' => ['id' => $fichierUpload[0]['id'],

                                ]

                            ]
                        ]

                    );

                } catch (\Exception $e) {

                    return ('une erreur est survenue' . $e->getMessage() . '. Le code erreur est' . $e->getCode());

                }

            return $this->rows;
    }

}}
