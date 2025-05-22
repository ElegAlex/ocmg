<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 07/11/18
 * Time: 10:30
 */

namespace AppBundle\Form;


use AppBundle\Entity\Data;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class dataType
 * @package AppBundle\Form
 */
class dataType extends AbstractType
{

    private $entityManager;

    /**
     * dataType constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        // recupération de l'entity manager
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'data',
            HiddenType::class,
            [

                'label_attr' => ['style' => 'display:none'],
                'mapped' => false,
                'required' => false,
                'auto_initialize' => false,
                'attr' => ['class' => 'class_data']

            ]

        )->add(

            'theme',
            themeType::class,
            [
                'attr' => ['class' => 'class_theme']
            ]

        )->add(

            'periode',
            periodeType::class,
            [
                'attr' => [
                    'class' => 'class_periode',
                    'style' => 'visibility:hidden'
                ],


            ]


        )
            ->add(

                'praticien',
                praticienType::class,
                [
                    'label_attr' => ['style' => 'display:none'],
                    'attr' => ['class' => 'class_praticien']
                ]
            );

        $builder->get('theme')->addEventListener(

            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                $form = $event->getForm();
                if ($event->getForm()->isSubmitted()) {

                    //si l'ageId est null, on affiche le composant de formulaire 'age
                    if ($this->entityManager->getRepository(Data::class)
                            ->SelectAgeIdFromData(
                                $form->get('theme')
                                    ->getData()
                                    ->getId())[0] !== ['age_id' => null]) {

                        $this->addAgeField($form->getParent());
                    }

                }
            }
        );
    }


    /**
     *
     *
     * @param FormInterface $form
     */
    private function addAgeField(FormInterface $form)
    {

        $form->add(
            'age',
            ageType::class,
            [
                'label_attr' => ['id' => 'label_age'],
                'attr' => ['class' => 'class_age']
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

                'data_class' => Data::class

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