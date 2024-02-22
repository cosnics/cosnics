<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Traits\DataClassExtensionTrait;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass
 */
class ComplexLearningPathItem extends ComplexContentObjectItem implements DataClassExtensionInterface
{
    use DataClassExtensionTrait;

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
