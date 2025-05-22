<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 17/09/18
 * Time: 11:45
 */

namespace App\Form;


use App\Entity\TextAccueil;
use CNAMTS\PHPK\CoreBundle\Form\Type\CollectionButtonType;
use CNAMTS\PHPK\CoreBundle\Generator\Form\Bouton;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class textAccueilType
 * @package App\Form
 */
class textAccueilType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('titreAccueil',
                TextType::class,
                [
                    'required' => true,
                    'attr' =>
                        [
                            'rows' => '10',
                            'cols' => '1000',
                            'maxlength' => '600',


                        ]

                ]
            )
            ->add('textAccueil',
                TextareaType::class,
                [
                    'label'=>'Texte Accueil',
                    'required' => true,
                    'attr' =>
                        [
                            'class' => 'ckeditor',
                            'rows' => '100',
                            'cols' => '100',
                            'width'=> '5000'

                        ]
                ]
            )
            ->add('saveAndAdd',
                CollectionButtonType::class,
                [

                    'collection' =>
                        [

                            'enregistrer le ciblage' =>
                                [

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

                'data_class' => TextAccueil::class

            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appBundle_textAccueil';
    }

}