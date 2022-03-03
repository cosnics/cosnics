<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportGroupsFormType extends AbstractType
{
    const ELEMENT_CSV_FILE = 'csv_file';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            self::ELEMENT_CSV_FILE, FileType::class
        );
    }
}
