<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 07/11/18
 * Time: 10:24
 */

namespace App\Form;


use App\Entity\Praticien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class praticienType
 * @package App\Form
 */
class praticienType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*@var $utaa Utaa*/
        $builder->add(
            'code_praticien',
            HiddenType::class,
            [
                'label' => 'code_praticien',
                'label_attr' =>
                    [
                        'style' => 'display:none'
                    ],
                'mapped' => false,
                'required' => false,
                'auto_initialize' => false,

            ]

        )
            ->add('utaa', depUtaaType::class,
                [
                    'auto_initialize' => false,
                    'label_attr' =>
                        [
                            'style' => 'display:none'
                        ]

                ]
            );
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [

                'data_class' => Praticien::class

            ]
        );
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_requeteglobale';

    }


}