<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.learning_path_item
 */
class ComplexLearningPathItem extends ComplexContentObjectItem
{
    const PROPERTY_PREREQUISITES = 'prerequisites';

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_PREREQUISITES);
    }

    public function get_prerequisites()
    {
        return $this->get_additional_property(self::PROPERTY_PREREQUISITES);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_complex_learning_path_item';
    }

    public function has_prerequisites()
    {
        return !StringUtilities::getInstance()->isNullOrEmpty(
            $this->get_additional_property(self::PROPERTY_PREREQUISITES)
        );
    }

    public function set_prerequisites($value)
    {
        $this->set_additional_property(self::PROPERTY_PREREQUISITES, $value);
    }
}
