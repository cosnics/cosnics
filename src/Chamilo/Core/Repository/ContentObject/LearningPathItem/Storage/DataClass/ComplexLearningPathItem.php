<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.content_object.learning_path_item
 */
class ComplexLearningPathItem extends ComplexContentObjectItem
{
    public const CONTEXT = LearningPathItem::CONTEXT;

    public const PROPERTY_PREREQUISITES = 'prerequisites';

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_PREREQUISITES];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_complex_learning_path_item';
    }

    public function get_prerequisites()
    {
        return $this->getAdditionalProperty(self::PROPERTY_PREREQUISITES);
    }

    public function has_prerequisites()
    {
        return !StringUtilities::getInstance()->isNullOrEmpty(
            $this->getAdditionalProperty(self::PROPERTY_PREREQUISITES)
        );
    }

    public function set_prerequisites($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_PREREQUISITES, $value);
    }
}
