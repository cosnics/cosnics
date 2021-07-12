<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryRubricResult extends DataClass
{
    const PROPERTY_CONTEXT_CLASS = 'context_class';
    const PROPERTY_CONTEXT_ID = 'context_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array()): array
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_CONTEXT_CLASS,
                self::PROPERTY_CONTEXT_ID
            )
        );
    }

    public static function get_table_name(): string
    {
        return 'repository_rubric_result';
    }
}