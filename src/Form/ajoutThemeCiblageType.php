<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/09/18
 * Time: 11:00
 */

namespace App\Form;

use App\Entity\Ciblage;
use CNAMTS\PHPK\CoreBundle\Form\Type\CollectionButtonType;
use CNAMTS\PHPK\CoreBundle\Generator\Form\Bouton;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ajoutThemeCiblageType
 * @package App\Form
 */
class ajoutThemeCiblageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('ciblage', TextType::class)
            // ajout du bouton au formulaire
            ->add(

                'saveAndAdd',

                CollectionButtonType::class,

                [

                    'collection' => [

                        'enregistrer le ciblage' => [

                            'label' => 'Ajouter',
                            'type'=>'submit'


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

                'data_class' => Ciblage::class

            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {

        return 'appBundle_ciblage';

    }
}