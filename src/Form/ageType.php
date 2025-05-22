<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 08/11/18
 * Time: 09:49
 */

namespace App\Form;

use App\Entity\Age;
use App\Repository\AgeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ageType
 * @package App\Form
 */
class ageType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $idTheme = $_POST['appbundle_requeteglobale']['theme']['theme'];

        $builder
            ->add('code_age', EntityType::class, [
                    'label' => 'Age',
                    'label_attr' => [
                        'style' => 'display:none',
                        'id' => 'code_age'
                    ],
                    'attr' => ['class' => 'code'],
                    'auto_initialize' => false,
                    'class' => Age::class,
                    'query_builder' => function (AgeRepository $ar) use ($idTheme) {

                        return $ar->createQueryBuilder('a')
                            ->join('a.datas', 'd')
                            ->join('d.theme', 't')
                            ->where('t.id=:id')
                            ->setParameter('id', $idTheme);

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

                'data_class' => Age::class,

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