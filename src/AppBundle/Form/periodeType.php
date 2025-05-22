<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 22/10/18
 * Time: 15:17
 */

namespace AppBundle\Form;


use AppBundle\Entity\Periode;
use AppBundle\Repository\PeriodeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class periodeType
 * @package AppBundle\Form
 */
class periodeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('code', EntityType::class, [
                    'label' => 'Période',
                    'auto_initialize' => false,
                    //'placeholder' => '--Sélectionner la période',
                    'class' => Periode::class,
                    'disabled'=>true,
                'attr'=>[
                    

                ],
                    'query_builder' => function (PeriodeRepository $pr) {

                        return $pr->createQueryBuilder('pr')
                            ->andWhere('pr.is_active = 1')
                            ->addOrderBy('pr.code', 'ASC');

                    }
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

                'data_class' => Periode::class,

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