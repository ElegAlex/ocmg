<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/09/18
 * Time: 11:00
 */

namespace App\Form;

use App\Entity\Periode;
use CNAMTS\PHPK\CoreBundle\Form\Type\CollectionButtonType;
use CNAMTS\PHPK\CoreBundle\Generator\Form\Bouton;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ajoutPeriodeType
 * @package App\Form
 */
class ajoutPeriodeType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('annee', TextType::class, [

                'label'=>'Année'
                

            ])
            ->add('is_semestre', ChoiceType::class, array(

                    'label' => 'les données concernent-elles le 1er semestre de l\'année ciblée?',

                    'attr'=>['class'=>'is_semestre'],

                    'choices' => [
                        'oui' => true,
                        'non' => false

                    ]
                )
            )
            // ajout du bouton au formulaire
            ->add(

                'saveAndAdd',

                CollectionButtonType::class,

                [

                    'collection' => [

                        'enregistrer la période' => [

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

                'data_class' => Periode::class

            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {

        return 'appBundle_periode';

    }

}