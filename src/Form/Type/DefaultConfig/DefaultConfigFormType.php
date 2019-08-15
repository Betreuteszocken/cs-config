<?php

namespace Betreuteszocken\CsConfig\Form\Type\DefaultConfig;

use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Betreuteszocken\CsConfig\Model\Form\DefaultConfig\DefaultConfigFormModel;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @property EntityType maps
 * @property EntityType mapCategories
 *
 * @package Betreuteszocken\CsConfig
 *
 */
class DefaultConfigFormType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('maps', EntityType::class, [
                'label'         => 'BZ Standard-Maps',
                'choice_label'  => 'name',
                'choice_value'  => 'id',
                'multiple'      => true,
                'class'         => Map::class,
                'query_builder' => function (EntityRepository $_mapRepository) {
                    return $_mapRepository->createQueryBuilder('map')
                                          ->addOrderBy('map.name', 'ASC')
                        ;
                },
                'choice_attr'   => function (Map $choice) {
                    $options = array();

                    if ($choice->isRemoved()) {
                        $options['data-custom-properties'] = 'removed';
                    }

                    return $options;
                },
                'attr'          => [
                    'class' => 'js-choice',
                ],
                'required'      => false
            ])
            ->add('mapCategories', EntityType::class, [
                'label'         => 'BZ Standard-Map-Kategorien',
                'choice_label'  => 'name',
                'choice_value'  => 'id',
                'multiple'      => true,
                'class'         => MapCategory::class,
                'query_builder' => function (EntityRepository $_mapCategoryRepository) {
                    return $_mapCategoryRepository->createQueryBuilder('map_category')
                                                  ->addOrderBy('map_category.name', 'ASC')
                        ;
                },
                'attr'          => [
                    'class' => 'js-choice',
                ],
                'required'      => false
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DefaultConfigFormModel::class,
        ));
    }
}
