<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 07/11/18
 * Time: 08:47
 */

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Utaa;
use App\Repository\DepartementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class depUtaaType
 * @package App\Form
 */
class depUtaaType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'departement',
            EntityType::class,

            [

                'label' => 'Département',
                'placeholder' => 'Tous les départements',
                'class' => Departement::class,
                'mapped' => false,
                'auto_initialize' => false,
                'query_builder' => function (DepartementRepository $rr) {

                    return $rr->createQueryBuilder('d')
                        ->join('d.region', 'r')
                        ->orderBy('d.codeDep', 'ASC')
                        ->where('r . is_active = 1')
                        ;

                }
            ]
        );

        $builder->get('departement')->addEventListener(

            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                $form = $event->getForm();

                /* @var $departement Departement */
                $departement = $form->getData();

                if ($event->getForm()->isSubmitted()) {

                    if (null !== $departement) {

                        $this->addCodeUtaaField($form->getParent(), $departement);

                    }

                }
            }
        );
    }


    /**
     * Rajoute le champs utaa en fonction du département choisi, au formulaire
     *
     * @param FormInterface $form
     * @param Departement $departement
     */
    private function addCodeUtaaField(FormInterface $form, Departement $departement)
    {

        /*@var $departement Departement*/
        $form->add('code_utaa', EntityType::class,
            [

                'label' => 'Utaa',
                'placeholder' => 'Toutes les Utaas',
                'class' => Utaa::class,
                'mapped' => false,
                'auto_initialize' => true,
                'choices' =>  $departement->getUtaas(),

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

                'data_class' => Utaa::class

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