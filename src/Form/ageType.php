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
use Symfony\Component\HttpFoundation\Request;

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

        // Retrieve the theme id either from options or from the request data
        $idTheme = $options['theme_id'] ?? null;

        // The field can be added dynamically without the option, in that case
        // try to read it from the submitted request (when available)
        if (null === $idTheme && isset($options['request'])) {
            $formData = $options['request']->request->get('appbundle_requeteglobale', []);
            if (isset($formData['theme']['theme'])) {
                $idTheme = $formData['theme']['theme'];
            }
        }

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
                'theme_id' => null,
                'request' => null,
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