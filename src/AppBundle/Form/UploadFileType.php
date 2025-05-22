<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 16/07/18
 * Time: 12:28
 */

namespace AppBundle\Form;

use AppBundle\Entity\FichierUpload;
use CNAMTS\PHPK\CoreBundle\Form\Type\CollectionButtonType;
use CNAMTS\PHPK\CoreBundle\Generator\Form\Bouton;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFileType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('filename', HiddenType::class,
                [
                    'required' => false,
                    'empty_data' => 'praticien',
                ]
            )
            ->add('file', FileType::class,
                [

                    'required' => true


                ]
            )
            // ajout du bouton au formulaire
            ->add(

                'saveAndAdd',

                CollectionButtonType::class,

                [

                    'collection' => [

                        'Charger le fichier' => [

                            'predefined' => Bouton::PREDEFINED_VALIDER,

                        ],
                    ],
                ]
            );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [

                'data_class' => FichierUpload::class

            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appBundle_fichierUpload';
    }
}