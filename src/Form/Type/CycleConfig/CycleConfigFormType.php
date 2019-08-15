<?php

namespace Betreuteszocken\CsConfig\Form\Type\CycleConfig;

use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Betreuteszocken\CsConfig\Entity\Map;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @property EntityType  mamiMaps
 * @property IntegerType userMaps
 * @property IntegerType defaultMaps
 * @property IntegerType defaultCategoryMaps
 * @property IntegerType originMaps
 * @property IntegerType randomMaps
 * @property IntegerType total
 *
 * @package Betreuteszocken\CsConfig
 */
class CycleConfigFormType extends AbstractType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        /** @var CycleConfig $cycleConfig */
        $cycleConfig = $options['data'];

        $builder
            ->add('mamiMaps', EntityType::class, [
                'label'         => 'MaMi-Maps',
                'choice_label'  => 'name',
                'choice_value'  => 'id',
                'multiple'      => true,
                'class'         => Map::class,
                'query_builder' => function (EntityRepository $_cycleConfigRepository) {
                    return $_cycleConfigRepository->createQueryBuilder('cycle_config')
                                                  ->addOrderBy('cycle_config.name', 'ASC')
                        ;
                },
                'attr'          => [
                    'class' => 'js-choice',
                ],
                'required'      => false
            ])
            ->add('userMaps', IntegerType::class, [
                'label' => 'Anzahl Forum-Nutzer-Maps',
                'attr'  => [
                    'min' => '0',
                ],
            ])
            ->add('defaultMaps', IntegerType::class, [
                'label' => 'Anzahl BZ-Standard-Maps',
                'attr'  => [
                    'min' => '0',
                ],
            ])
            ->add('defaultCategoryMaps', IntegerType::class, [
                'label' => 'Anzahl Maps aus BZ-Standard-Kategorien',
                'attr'  => [
                    'min' => '0',
                ],
            ])
            ->add('originMaps', IntegerType::class, [
                'label' => 'Anzahl Standard CS1.6 Maps',
                'attr'  => [
                    'min' => '0',
                ],
            ])
            ->add('randomMaps', IntegerType::class, [
                'label' => 'Anzahl Zufalls-Maps',
                'attr'  => [
                    'min' => '0',
                ],
            ])
            ->add('total', IntegerType::class, [
                'label'    => 'Gesamt',
                'mapped'   => false,
                'disabled' => true,
                'data'     => $cycleConfig->getTotalMaps()
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CycleConfig::class,
        ));
    }
}
