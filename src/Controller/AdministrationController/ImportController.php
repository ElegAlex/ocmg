<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 12/06/18
 * Time: 15:41
 */

namespace App\Controller\AdministrationController;

use App\Entity\Age;
use App\Entity\Ciblage;

use App\Entity\Data;
use App\Entity\Region;
use App\Entity\FichierUpload;
use App\Entity\Periode;
use App\Entity\Praticien;
use App\Entity\Theme;
use App\Entity\Utaa;
use App\Entity\Ville;
use App\Repository\CiblageRepository;
use App\Form\UploadFileType;
use App\Tableaux\ListeCiblageUpload;
use App\Tableaux\ListePraticiensUpload;
use App\Tableaux\ListeUpload;
use CNAMTS\PHPK\CoreBundle\Controller\AbstractController;
use Doctrine\DBAL\Types\Type;
use League\Csv\Reader;
use League\Csv\Statement;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class ImportController
 * @package App\Controller\AdministrationController
 */
class ImportController extends AbstractController
{


    /**
     * methode permet chargement du fichier .csv des praticiens, le contrôle et le dispatch des datas en base de données
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function uploadpraticienAction(Request $request)
    {
        $fichierUpload = new FichierUpload();


        $form = $this->createForm(

            UploadFileType::class,

            $fichierUpload

        );

        // gestion de la soumission du formulaire

        $form->handleRequest($request);
        $filename = $this->getRepository(FichierUpload::class)->findAll();
        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires

        if (empty($filename)) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    // $file stocke le fichier téléchargé CSV file.
                    // modification du php.ini pour upload de large file

                    /** @var $file */
                    $file = $fichierUpload->getFile();

                    //controle nom du fichier
                    if ((explode('_', $file->getClientOriginalName())[0] !== 'liste') &&
                        'mg'
                        !==
                        mb_strtolower(explode('_', $file->getClientOriginalName())[1])) {

                        //recupère la période active unique
                        $periodeactive = $this->getDoctrine()->getManager()->getRepository(Periode::class)->findBy(['is_active' => 1]);

                        $this->notification(
                            'Le fichier  importé ne correspond pas à un fichier de praticiens. Il doit être nommé conventionnellement liste_mg_' . $periodeactive[0]->getCode() . '.csv ', 'error');

                        return $this->redirect(

                            $this->generateUrl(

                                'app_import_fichier_liste'

                            )

                        );

                    }
                    $fileName = $fichierUpload->upload();

                    $reader = Reader::createFromPath('%kernel.root_dir%/../../src/AppBundle/Entitytmp/' . basename($file->getFilename()), 'r');

                    $reader->setDelimiter(';');

                    $reader->setHeaderOffset(0);

                    $reader->jsonSerialize();

                    //nombre de champs dans le fichier uploadé
                    $nbColUploadFile = count($reader->getHeader());

                    $stmt = new Statement();

                    $records = $stmt->process($reader);

                    $records->jsonSerialize();

                    //nombre de lignes contenues dans le fichier uploadé
                    $nbRowUploadFile = count($records->jsonSerialize());

                    //récupère les entêtes du fichier uploadé
                    $colonneThemes = $records->getHeader();
                    //controle des entêtes
                    $champFilePraticien = ['DPT', 'LIB_COM', 'UTAA', 'NUM_PS', 'CLE', 'NOM', 'PRENOM'];

                    if (array_diff($colonneThemes, $champFilePraticien) !== []) {


                        $this->notification('Erreurs de champs dans le fichier des praticiens:' . implode(',', array_diff($colonneThemes, $champFilePraticien)), 'error');

                        return $this->redirect(

                            $this->generateUrl(

                                'app_import_fichier_liste'

                            )

                        );
                    }

                    //liste des utaa actives
                    $utaasCiblées = [];

                    $em = $this->getDoctrine()->getManager();
                    foreach ($em->getRepository(Region::class)->departementsEtUtaaRegionActive() as $utaaActive) {

                        $utaasCiblées[] = $utaaActive['code_utaa'];

                    }

                    //preload praticiens, utaas and villes for quick lookup
                    $existingPraticiens = [];
                    foreach ($em->getRepository(Praticien::class)->findAll() as $p) {
                        $existingPraticiens[$p->getCodePraticien()] = $p;
                    }

                    $existingUtaas = [];
                    foreach ($em->getRepository(Utaa::class)->findAll() as $u) {
                        $existingUtaas[$u->getCodeUtaa()] = $u;
                    }

                    $existingVilles = [];
                    foreach ($em->getRepository(Ville::class)->findAll() as $v) {
                        $existingVilles[$v->getLibelleVille()] = $v;
                    }

                    //controlle que le numéro de praticiens existe
                    $compteur = 1;
                    $batchSize = 50;
                    $processed = 0;
                    foreach ($records as $record) {
                        $compteur++;

                        if ($record['NUM_PS'] === "") {

                            $this->notification(' pas de numéro de praticien à la ligne ' . $compteur, 'error');

                            return $this->redirect(

                                $this->generateUrl(

                                    'app_import_fichier_liste'

                                )

                            );

                        }

                        //controlle que le nom du praticien existe
                        if ($record['NOM'] === "") {

                            $this->notification(' pas de nom de praticien à la ligne ' . $compteur, 'error');

                            return $this->redirect(

                                $this->generateUrl(

                                    'app_import_fichier_liste'

                                )

                            );

                        }

                        //vérifie que le praticien n'existe pas déjà
                        $praticien = $existingPraticiens[$record['NUM_PS']] ?? null;

                        //si le praticien n 'existe pas il rentre en base de données
                        if (null === $praticien) {
                            //si le numéro de praticien n 'est pas égal à 8 chiffres, =>rejet du fichier importé
                            if (mb_strlen($record['NUM_PS']) !== 8) {

                                $this->notification('numero de praticien doit avoir 8 chiffres' . $record['NUM_PS'] . ' ligne ' . $compteur, 'error');

                                return $this->redirect(

                                    $this->generateUrl(

                                        'app_import_fichier_liste'

                                    )
                                );
                            }
                            // nouveau praticien
                            $praticien = (new Praticien())
                                ->setCodePraticien($record['NUM_PS'])
                                ->setClePrat($record['CLE'])
                                ->setNomPrat($record['NOM'])
                                ->setPrenomPrat($record['PRENOM']);

                            $em->persist($praticien);
                            $existingPraticiens[$record['NUM_PS']] = $praticien;


                        }

                        //vérifie si l'utaa du fichier est dans la liste des utaas de la région ciblée
                        if (array_search($record['UTAA'], $utaasCiblées) === false) {


                            $this->notification(' utaa hors région définie ' . $record['UTAA'], 'error');

                            return $this->redirect(

                                $this->generateUrl(

                                    'app_import_fichier_liste'

                                )

                            );

                        }
                        $utaa = $existingUtaas[$record['UTAA']] ?? null;


                        //recherche si le libelle de la ville existe
                        $ville = $existingVilles[$record['LIB_COM']] ?? null;

                        //si n'existe pas ajoute une nouvelle ville
                        if (null === $ville) {

                            $ville = (new Ville())
                                ->setLibelleVille($record['LIB_COM']);
                            $this->notification('Le fichier uploadé contient une nouvelle commune, elle vient d\'être créée avec succès', 'success');
                            $em->persist($ville);
                            $existingVilles[$record['LIB_COM']] = $ville;

                        }

                        $em->persist($ville);

                        // relie les utaa et ville au praticien
                        $praticien->setUtaa($utaa);
                        $praticien->setVille($ville);

                        $em->persist($praticien);

                        $processed++;
                        if ($processed % $batchSize === 0) {
                            $em->flush();
                        }

                    }


                    // flush remaining entities
                    $em->flush();


                    //récupération des variables du fichier uploadé
                    $fileSize = $file->getClientSize();

                    $path = $file->getPathname();

                    $originalName = $file->getClientOriginalName();

                    // création de l'entité fichierUPload
                    $fichierUpload
                        ->setFile($fileName)
                        ->setFileSize($fileSize)
                        ->setPath($path)
                        ->setOriginalName($originalName)
                        ->setNbRowUploadFile($nbRowUploadFile)
                        ->setNbColUploadFile($nbColUploadFile)
                        ->setNbRowUploaded(0);

                    //nombre de lignes uploadées
                    $nbRowUploaded = $em->getRepository(Praticien::class)->count([]);

                    // ajoute à l'entité le nombre de lignes uploadées
                    $fichierUpload->setNbRowUploaded($nbRowUploaded);

                    $em->persist($fichierUpload);

                    $response = new Response();

                    // compare le nombre de lignes du fichier initial avec le nombre d'entités uploadées
                    if ($nbRowUploadFile !== $nbRowUploaded) {


                        $fichierUpload->setErrorCode($response->getStatusCode());
                        throw new Exception('Une erreur est survenue lors du chargement du fichier. Supprimez le fichier Praticien et relancez l\'import');

                    }


                    $dateUpload = date_format($fichierUpload->getUpdatedAt(), 'd/m/y');

                    $fichierUpload->setErrorCode($response->getStatusCode());

                    $this->notification(
                        'le chargement du fichier a été effectué avec succès le: ' . $dateUpload);


                    $em->persist($fichierUpload);

                    $em->flush();

                    return $this->redirect(

                        $this->generateUrl(

                            'app_import_fichier_liste'

                        )

                    );


                } catch (\Exception $e) {

                    // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                    $this->error('une erreur est survenue doublon de praticien ' . $e->getMessage());


                }
            }

        }


        return $this->render(

            'Admin/Import/fichier.html.twig',

            [

                'form' => $form->createView(),

            ]

        );

    }


    /**
     *
     * methode permet chargement du fichier .csv des ciblages et le dispatch des datas en base de données
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function uploadciblageAction(Request $request)
    {

        set_time_limit(0);
        ini_set('memory_limit', -1);

        $fichierUpload = new FichierUpload();

        $form = $this->createForm(

            UploadFileType::class,

            $fichierUpload

        )
            ->add(

                'filename', EntityType::class, [

                    'required' => false,
                    'placeholder' => '--Sélectionnez un ciblage',
                    'class' => Ciblage::class,
                    'mapped' => true,
                    'attr' => [

                        'style' => 'visibility:hidden'

                    ],
                    'query_builder' => function (CiblageRepository $c) {

                        return $c->createQueryBuilder('c')->orderBy('c.ciblage', 'ASC');

                    }
                ]
            );

        // gestion de la soumission du formulaire

        $form->handleRequest($request);

        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires
        if ($form->isSubmitted() && $form->isValid()) {
            try {

                // $file stocke le fichier téléchargé CSV file.
                // modification du php.ini pour upload de large file

                /** @var $file */

                $file = $fichierUpload->getFile();
                //controle nom du fichier
                if ((explode('_', $file->getClientOriginalName())[0] !== 'ciblage') &&
                    mb_strtolower(mb_substr($fichierUpload->getFilename()->getCiblage(), 0, 5))
                    !==
                    mb_strtolower(mb_substr(explode('_', $file->getClientOriginalName())[1], 0, 5))) {

                    $this->notification(
                        'Le fichier importé ne correspond pas au ciblage sélectionné.
                         Par convention, il doit être nommé ciblage_' . mb_strtolower($fichierUpload->getFilename()->getCiblage()) . '_' . $this->getRepository(Periode::class)->findBy(['is_active' => true])[0]->getCode() . '.csv',
                        'error');
                    return $this->redirect(

                        $this->generateUrl(

                            'app_import_fichier_liste'

                        )

                    );

                }

                $fileName = $fichierUpload->upload();

                $reader = Reader::createFromPath('%kernel.root_dir%/../../src/AppBundle/Entitytmp/' . basename($file->getFilename()), 'r');

                $reader->setDelimiter(';');

                $reader->setHeaderOffset(0);

                $reader->jsonSerialize();

                //nombre de champs dans le fichier uploadé
                $nbColUploadFile = count($reader->getHeader());

                $stmt = new Statement();

                $records = $stmt->process($reader);

                $records->jsonSerialize();

                //nombre de lignes contenues dans le fichier uploadé
                $nbRowUploadFile = count($records->jsonSerialize());

                //récupère les entêtes du fichier uploadé
                $colonneThemes = $records->getHeader();


                $keyPeriode = array_search('periode', $colonneThemes);
                if (false === $keyPeriode) {

                    $this->notification('une erreur est survenue: vérifier la synthaxe du champ "periode" en minuscules et sans accents ni espaces', 'error');

                    return $this->redirectToRoute('app_import_fichier_liste');
                }

                $keyNumPS = array_search('NUM_PS', $colonneThemes);

                if (false === $keyNumPS) {

                    $this->notification('une erreur est survenue: vérifier la synthaxe du champ "NUM_PS" en majuscules, "_" et sans espaces', 'error');

                    return $this->redirectToRoute('app_import_fichier_liste');
                }

                $keyClasseAge = array_search('CLA_AGE', $colonneThemes);

                if ($nbColUploadFile !== $keyPeriode + 1) {

                    if (false === $keyClasseAge) {

                        $this->notification('une erreur est survenue: vérifier la synthaxe du champ "CLA_AGE" en majuscules, "_" et sans espaces', 'error');

                        return $this->redirectToRoute('app_import_fichier_liste');
                    }
                }

                $libelleTheme = $fichierUpload->getFilename();

                $em = $this->getDoctrine()->getManager();

                //recherche par colonneTheme si le thème existe
                $theme = $em->getRepository(Theme::class)
                    ->findOneBy([
                        'libelleTheme' => $libelleTheme

                    ]);


                //set data dans objet Theme
                if (null === $theme) {

                    $theme = new Theme();
                    $theme->setLibelleTheme($libelleTheme);
                    $theme->setColonneTheme($colonneThemes);


                }
                $compteur = 1;
                foreach ($records as $key => $record) {
                    $compteur++;
                    if ($key === 1) continue;

                    $data = new Data();
                    $data->setDatas(array_values($record));

                    $em->persist($data);

                    foreach ($record as $k => $value) {

                        if ($k === 'periode') {

                            $newPeriode = $value;

                            $periode = $em->getRepository(Periode::class)
                                ->findOneBy([

                                    'code' => $newPeriode

                                ]);

                            if (null === $periode) {

                                $this->notification('une erreur est survenue: Erreur période du fichier ou erreur paramétrage de la période ciblée', 'error');

                                return $this->redirectToRoute('app_import_fichier_liste');
                            }

                            $data->setPeriode($periode);

                        }

                        if ($k === 'NUM_PS') {

                            $newPraticien = $value;

                            $praticien = $em->getRepository(Praticien::class)
                                ->findOneBy([

                                    'codePraticien' => $newPraticien

                                ]);
                            //bloquant si le fichier des praticiens n est pas complet à débloquer pour le test

                            if (null === $praticien) {

                                $this->notification('une erreur est survenue: Le praticien dont le numéro de praticien est ' . $newPraticien . ' à la ligne ' . $compteur . ' n\'est pas enregistré en base', 'error');

                                return $this->redirectToRoute('app_import_fichier_liste');
                            }

                            $data->setPraticien($praticien);

                        }

                        if ($k === 'CLA_AGE') {

                            $newAge = $value;

                            $age = $em->getRepository(Age::class)
                                ->findOneBy([

                                    'codeAge' => $newAge

                                ]);

                            if (null === $age) {
                                // créer une nouvelle tranche d'âge

                                $age = (new Age())
                                    ->setCodeAge($newAge);
                                $this->notification('Le fichier uploadé contient une nouvelle classe d\'âge, elle vient d\'être créée avec succès', 'success');
                                $em->persist($age);
                            }
                            $data->setAge($age);
                        }
                        $data->setTheme($theme);
                    }
                }

                //récupère la taille du fichier uploadé
                $fileSize = $file->getClientSize();
                //récupère le chemin du fichier temporaire
                $path = $file->getPathname();
                //récupère le nom original du fichier .csv uploadé
                $originalName = $file->getClientOriginalName();

                //nouvelle entite fichierUpload
                $fichierUpload
                    ->setFile($fileName)
                    ->setFileSize($fileSize)
                    ->setPath($path)
                    ->setOriginalName($originalName)
                    ->setNbRowUploadFile($nbRowUploadFile)
                    ->setNbColUploadFile($nbColUploadFile)
                    ->setNbRowUploaded(0);

                $em->persist($theme);

                $em->persist($fichierUpload);

                // Flush des modifications pour que l'insertion du  nom du fichierUpload se fasse en base

                $em->flush();

                $dateUpload = date_format($fichierUpload->getUpdatedAt(), 'd/m/y');

                $ciblage = $libelleTheme->getCiblage();

                $theme = $em->getRepository(Theme::class)->findBy(['libelleTheme' => $ciblage])[0];

                $nbRowUploaded = count($em->getRepository(Data::class)->findBy(['theme' => $theme])) + 1;

                $fichierUpload->setNbRowUploaded($nbRowUploaded);

                $em->persist($fichierUpload);


                $response = new Response();
                if ($nbRowUploadFile !== $nbRowUploaded) {
                    $statusCode = $response->getStatusCode();
                    $fichierUpload->setErrorCode($statusCode);
                    throw new Exception('Une erreur est survenue lors du chargement du fichier. Supprimez le fichier ciblé et relancez l\'import');
                }
                $statusCode = $response->getStatusCode();
                $fichierUpload->setErrorCode($statusCode);
                $em->persist($fichierUpload);
                $em->flush();
                $this->notification(

                    'le chargement du fichier a été effectué avec succès le: ' . $dateUpload);

                return $this->redirect(

                    $this->generateUrl(

                        'app_import_fichier_commentaire'

                    )

                );

            } catch (\Exception $e) {

                // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                $this->error('une erreur est survenue' . $e->getMessage());

            }
        }

        return $this->render(

            'Admin/Import/fichier.html.twig',

            [

                'form' => $form->createView()

            ]

        );

    }


    /**
     * methode permet chargement du fichier .csv des ciblages et le dispatch des datas en base de données
     *
     * @param Request $request
     *
     * @return RedirectResponse| Response
     */
    public function uploadcommentaireAction(Request $request)
    {
        $fichierUpload = new FichierUpload();

        $fichiersUploadés = $this->getRepository(FichierUpload::class)->findAll();

        $form = $this->createForm(

            UploadFileType::class,

            $fichierUpload

        )
            ->add('filename', HiddenType::class, [
                    'required' => false,
                    'empty_data' => end($fichiersUploadés)->getFilename()
                ]
            );

        // gestion de la soumission du formulaire

        $form->handleRequest($request);

        //Si le formulaire répond aux règles de validation, on effectue les actions nécessaires
        if ($form->isSubmitted() && $form->isValid()) {
            try {

//               $file stocke le fichier téléchargé CSV file.
//               modification du php.ini pour upload de large file

                /** @var $file */
                $file = $fichierUpload->getFile();

                //vérification du nom du fichier
                if ((explode('_', $file->getClientOriginalName())[0] !== 'lib') &&
                    mb_strtolower(mb_substr($fichierUpload->getFilename()->getCiblage(), 0, 5))
                    !==
                    mb_strtolower(mb_substr(explode('_', $file->getClientOriginalName())[1], 0, 5))) {

                    $periodeactive = $this->getDoctrine()->getManager()->getRepository(Periode::class)->findBy(['is_active' => 1]);
                    $theme = mb_strtolower(end($fichiersUploadés)->getFilename());

                    $this->notification(
                        'Le fichier libellés importé ne correspond pas au ciblage sélectionné . Par convention, il doit être nommé: lib_' . $theme . '_' . $periodeactive[0]->getCode() . '.csv', 'error');
                    return $this->redirect(

                        $this->generateUrl(

                            'app_import_fichier_commentaire'

                        )

                    );

                }

                $fileName = $fichierUpload->upload();

                $reader = Reader::createFromPath('%kernel.root_dir%/../../src/AppBundle/Entitytmp/' . basename($file->getFilename()), 'r');

                $reader->setDelimiter(';');

                $reader->setHeaderOffset(0);

                $reader->jsonSerialize();

                //nombre de champs dans le fichier uploadé
                $nbColUploadFile = count($reader->getHeader());

                $stmt = new Statement();

                $records = $stmt->process($reader);
                $records->jsonSerialize();

                //nombre de lignes contenues dans le fichier uploadé
                $nbRowUploadFile = count($records->jsonSerialize());

                //récupère les entêtes du fichier uploadé
                $colonneThemes = $records->getHeader();

                $commentaireTheme = array_values($records->getRecords()->current());
                $em = $this->getDoctrine()->getManager();

                //Vérification de l'égalité de nombre de colonnes entre les deux fichiers ciblage et libelles
                $nbColCiblageUploadFile = $em->getRepository(FichierUpload::class)->findOneBy(['filename' => end($fichiersUploadés)->getFilename()])->getNbColUploadFile();

                if ($nbColUploadFile !== $nbColCiblageUploadFile) {

                    $this->notification('Le fichier "libellés" ne contient pas le même nombre de colonnes que le fichier "ciblage"_ ' . end($fichiersUploadés)->getFilename() . ' Vérifiez les fichiers importés', 'error');

                    return $this->redirectToRoute('app_import_fichier_liste');
                }


                //comparaison des entêtes des deux fichiers
                $colCiblageUlploadFile = $em->getRepository(Theme::class)->findOneBy(['libelleTheme' => end($fichiersUploadés)->getFilename()])->getColonneTheme();

                $diffCol = array_diff($colonneThemes, $colCiblageUlploadFile);

                if ($diffCol !== []) {

                    $this->notification('Différences constatées entre les champs des fichiers "libellés" et "ciblage"_ ' . end($fichiersUploadés)->getFilename() . ' : ' . implode(',', $diffCol), 'error');

                    return $this->redirectToRoute('app_import_fichier_liste');

                }

                //recherche par colonneTheme si le thème existe
                $theme = $em->getRepository(Theme::class)
                    ->findOneBy([
                        'libelleTheme' => end($fichiersUploadés)->getFilename()

                    ]);


                //set commentaires dans objet Theme
                if ($theme instanceof Theme) {

                    $libelleTheme = $fichierUpload->getFilename();

                    $fichierUpload->setFilename('libellés-' . $libelleTheme);

                    $theme->setCommentaireColonne($commentaireTheme);

                    // $em->persist($fichierUpload);
                    $em->persist($theme);

                }

                //récupère la taille du fichier uploadé
                $fileSize = $file->getClientSize();
                //récupère le chemin du fichier temporaire
                $path = $file->getPathname();
                //récupère le nom original du fichier .csv uploadé
                $originalName = $file->getClientOriginalName();

                //nouvelle entite fichierUpload
                $fichierUpload
                    ->setFile($fileName)
                    ->setFileSize($fileSize)
                    ->setPath($path)
                    ->setOriginalName($originalName)
                    ->setNbRowUploadFile($nbRowUploadFile)
                    ->setNbColUploadFile($nbColUploadFile)
                    ->setNbRowUploaded(0);


                $em->persist($fichierUpload);

                $dateUpload = date_format($fichierUpload->getUpdatedAt(), 'd/m/y');


                $response = new Response();

                //vérifier que les libellés sont bien chargés

                if (null === $theme->getCommentaireColonne()) {

                    $statusCode = $response->getStatusCode();
                    $fichierUpload->setErrorCode($statusCode);
                    throw new Exception('Une erreur est survenue lors du chargement du fichier. Supprimez le fichier  et relancez l\'import');

                }
                $statusCode = $response->getStatusCode();
                $fichierUpload->setErrorCode($statusCode);
                $fichierUpload->setNbRowUploaded(1);
                $em->persist($fichierUpload);

                $this->notification(

                    'le chargement du fichier a été effectué avec succès le: ' . $dateUpload);

                $em->flush();

                return $this->redirect(

                    $this->generateUrl(

                        'app_import_fichier_liste'

                    )

                );

            } catch (\Exception $e) {

                // En cas d'erreur dans le bloc Try on affiche le détail à l'utilisateur
                $this->error('une erreur est survenue' . $e->getMessage());

            }
        }

        return $this->render(

            'Admin/Import/fichier.html.twig',

            [

                'form' => $form->createView(),
                'libelleTheme' => end($fichiersUploadés)->getFilename()

            ]

        );

    }

    /**
     * affiche un tableau de fichier uploade
     * @return type | Response
     *
     */

    public function listeAction()
    {


        $fichiersUpload =
            $this->getRepository(FichierUpload::class)
                ->findAll();

        //appel de la structure du tableau crée dans la classe listeUpload
        $tableau = $this->get('phpk_core.tableau')->get(new ListeUpload());

        // récupère les datas de la méthode liste du repository
        $tableau
            ->getDataHandler()
            ->setRepository($this
                ->getRepository(FichierUpload::class));

        //associer une nouvelle route au tableau
        $tableau->setRoute('app_import_fichier_detail');

        return $this->render(

            'Admin/Import/Liste/liste.html.twig',

            [
                'fichiersUpload' => $fichiersUpload,

                'listeUpload' => $tableau

            ]

        );

    }


    /**
     * affiche le détail d'un fichier chargé
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function detailAction(Request $request, $id): Response
    {
        $session = new Session();
        $session->remove('filenameUpload', 'themeId');

        $fichiersUpload =
            $this->getRepository(FichierUpload::class)
                ->findOneById($id);

        //si le fichierupload est praticien
        if ($fichiersUpload->getFilename() === 'praticien') {
            $theme =
                $this->getRepository(Theme::class)
                    ->findAll();
            //appel de la structure du tableau crée dans la classe listePraticiensUpload
            $tableau = $this->get('phpk_core.tableau')->get(new ListePraticiensUpload());

            // récupère les datas de la méthode liste du repository
            $tableau
                ->getDataHandler()
                ->setRepository($this
                    ->getRepository(Praticien::class));


            return $this->render(
                'Admin/Import/Liste/detail.html.twig',
                ['fichiersUpload' => $fichiersUpload,
                    'theme' => $theme,
                    'listePraticiensUpload' => $tableau]

            );

        } //pour les fichiers upload ciblage et commentaire ciblage
        else {

            $session->set('filenameUpload', []);

            $filenameUpload[] = $fichiersUpload->getFilename();
            $filenameUpload = explode('-', $filenameUpload[0]);

            if (count($filenameUpload) === 2) {
                //si c'est un commentaire ciblage
                $session->set('filenameUpload', $filenameUpload[1]);


            } else {

                $session->set('filenameUpload', $filenameUpload);

            }

            $theme =
                $this->getRepository(Theme::class)
                    ->findOneBy([

                        'libelleTheme' => $session->get('filenameUpload')

                    ]);

            $session->set('themeId', []);

            $themeId[] = $theme->getId();

            $session->set('themeId', $themeId);

            $session->set('colonneTheme', []);

            $colonneTheme = array_keys($this->getRepository(Data::class)->liste()[0]);

            $session->set('colonneTheme', $colonneTheme);

            //appel de la structure du tableau crée dans la classe listeCiblageUpload
            $tableau = $this->get('phpk_core.tableau')->get(new ListeCiblageUpload());

            // récupère les datas de la méthode liste du repository

            if ($filenameUpload[0] === 'commentaire') {

                $tableau->getDataHandler()->setRepository($this->getRepository(Theme::class));

            } else {


                $tableau->getDataHandler()->setRepository($this->getRepository(Data::class));

            }


            return $this->render(
                'Admin/Import/Liste/detail.html.twig',
                ['fichiersUpload' => $fichiersUpload,

                    'theme' => $theme,

                    'listeCiblageUpload' => $tableau]

            );

        }
    }


    /**
     * méthode permet de supprimer un fichier uploadé ainsi que les datas en BDD
     *
     *
     * @param FichierUpload $fichierUpload
     * @return RedirectResponse
     */
    public function deleteAction(FichierUpload $fichierUpload): Response
    {

        $em = $this->getDoctrine()->getManager();

        $filename[] = $fichierUpload->getFilename();

        $filenameUpload = explode('-', $filename[0]);

        //si 'commentaire-*' est supprimé , il entraine la suppression de *
        if (count($filenameUpload) === 2) {
            //si c'est un ciblage
            $filenameUpload = $filenameUpload[1];
            $binomeFile = $filenameUpload;

        } //si le nom du fichier est praticien
        elseif ($filenameUpload[0] === 'praticien') {


            $binomeFile = 'praticien';

        } //si le nom du fichier est * il entraine la suppressionde commentaire-*
        else {
            //si c'est un commentaire ciblage
            $filenameUpload = $filenameUpload[0];
            $binomeFile = 'commentaire-' . $filenameUpload;

        }

        $theme = $em->getRepository(Theme::class)
            ->findOneBy([
                'libelleTheme' => $filenameUpload,

            ]);


        if (null !== $theme) {
            $em->remove($theme);
        }

        //récupère un tableau de praticiens
        $praticiens = $em->getRepository(Praticien::class)->findAll();

        //supprime chaque praticien

        if (($filename[0] === 'praticien') && ($theme === null)) {

            foreach ($praticiens as $praticien) {

                $em->remove($praticien);
                //$em->flush();
            }

        }


        //effacer le fichier tmp
        // unlink($fichierUpload->getUploadRootDir() . '/' . basename($fichierUpload->getPath()));

        $fichierUploadCorrespondant = $em->getRepository(FichierUpload::class)
            ->findOneBy([

                'filename' => $binomeFile

            ]);


        //supprime le fichier uploadé
        $em->remove($fichierUpload);
        if (null !== $fichierUploadCorrespondant) {
            $em->remove($fichierUploadCorrespondant);
        }
        $em->flush();

        //récupère un tableau de praticiens
        $ciblages = $em->getRepository(Ciblage::class)->findAll();

        //supprime chaque ciblage
        foreach ($ciblages as $ciblage) {

            $ciblageTheme = $ciblage->getCiblage();

            //message personnalisé
            if ($filename === 'praticien') {

                $this->addFlash('success', 'le fichier des praticiens a été supprimé avec succès');

            } elseif ($binomeFile === $ciblageTheme || $binomeFile === 'commentaire-' . $ciblageTheme) {

                $this->addFlash('success', 'les fichiers ciblage et commentaire ' . $ciblageTheme . ' ont été supprimés avec succès');

            }
        }

        return $this->redirectToRoute('app_import_fichier_liste');

    }

}














