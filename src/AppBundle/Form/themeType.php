<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 08/11/18
 * Time: 09:09
 */

namespace AppBundle\Form;


use AppBundle\Entity\Theme;
use AppBundle\Repository\ThemeRepository;
use AppBundle\Tools\JsonHandler;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class themeType
 * @package AppBundle\Form
 */
class themeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('theme', EntityType::class,
                [
                    'label' => 'Thème',
                    'placeholder' => '--Sélectionner un thème',
                    'class' => Theme::class,
                    'mapped' => false,
                    'attr' =>
                        [
                            'class' => 'theme_list'
                        ],

                    'auto_initialize' => false,
                    'query_builder' => function (ThemeRepository $tr) {

                        return $tr->createQueryBuilder('tr')
                            ->orderBy('tr.commentaireColonne', 'ASC');

                    }
                ]
            );

        $builder
            ->get('theme')
            ->addEventListener(FormEvents::POST_SUBMIT,
                function (FormEvent $event) /*use ($themeId)*/ {


                    $form = $event->getForm()->getNormData();

                    if ($event->getForm()->isSubmitted() && ($form !== null)) {

                        //appel de la méthode privée addCommentaireField
                        // qui prend en paramètres le formulaire parent et le tableau de commentaires
                        $this->addCommentaireField(
                            $event->getForm()->getParent(), $this->selectCommentaires($form));

                    }
                }

            );
    }

    /**
     *
     * Rajoute uen liste de checkbox "commentaires" au formulaire
     *
     * @param FormInterface $form
     * @param $commentaires
     */
    private function addCommentaireField(FormInterface $form, $commentaires)
    {

        $form->add('allComments', CheckboxType::class, [

            'label' => 'Tous les indicateurs',
            'value' => 'allComments',
            'mapped' => false,
            'required' => false,
            'auto_initialize' => false,
            'attr' =>
                [
                    'checked' => 'checked',
                ],
            'label_attr' =>
                [
                    'style' => 'display:none'
                ]


        ]);

        foreach ($commentaires as $key => $commentaire) {

            $form->add($key, CheckboxType::class, [

                    'label' => $commentaire,
                    'mapped' => false,
                    'required' => false,
                    'value' => $commentaire,
                    'auto_initialize' => false,
                    'attr' =>
                        [
                            'class' => 'libelles'
                        ],
                    'label_attr' => [

                        'style' => 'display:none'

                    ]

                ]

            );

        }
    }

    public function selectCommentaires($form)
    {


        //Exclure les commentaires qui ne rentrent pas dans la requête
        $hideDatas =
            [

                'Numero du prescripteur',
                'Annee ou semestre',
                'Classe d\'age'
            ];

        //créer le tableau de commentaires
        $commentaires = JsonHandler::mergeData(
            array_keys(
                array_diff(
                    array_values($form->getCommentaireColonne()),
                    $hideDatas
                )
            ),
            array_diff(
                array_values($form->getCommentaireColonne()),
                $hideDatas
            )
        );

        return $commentaires;

    }







//TODO gerer l affichage du commentaire label

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [

                'data_class' => Theme::class

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