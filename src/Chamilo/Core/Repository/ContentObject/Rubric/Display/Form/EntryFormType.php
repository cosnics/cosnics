<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EntryFormType
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Form
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class EntryFormType extends AbstractType
{
    const ELEMENT_RUBRIC_RESULTS = 'rubric_results';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::ELEMENT_RUBRIC_RESULTS, HiddenType::class);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'rubric_entry';
    }
}
