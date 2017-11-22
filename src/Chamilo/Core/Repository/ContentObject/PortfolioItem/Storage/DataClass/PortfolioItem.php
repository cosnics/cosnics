<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\HelperContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.portfolio_item
 */
class PortfolioItem extends ContentObject implements Versionable, HelperContentObjectSupport
{
    const PROPERTY_REFERENCE = 'reference_id';

    /**
     *
     * @var Portfolio
     */
    private $reference_object;

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_REFERENCE);
    }

    public function get_reference()
    {
        return $this->get_additional_property(self::PROPERTY_REFERENCE);
    }

    public function set_reference($reference)
    {
        $this->set_additional_property(self::PROPERTY_REFERENCE, $reference);
    }

    public function get_reference_object()
    {
        if (! $this->reference_object instanceof Portfolio)
        {
            $this->reference_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $this->get_reference());
        }
        return $this->reference_object;
    }
}
