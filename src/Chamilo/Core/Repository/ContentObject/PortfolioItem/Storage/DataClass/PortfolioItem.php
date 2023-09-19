<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\HelperContentObjectSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass
 */
class PortfolioItem extends ContentObject implements Versionable, HelperContentObjectSupportInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\PortfolioItem';

    public const PROPERTY_REFERENCE = 'reference_id';

    /**
     * @var Portfolio
     */
    private $reference_object;

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_REFERENCE];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_portfolio_item';
    }

    public function get_reference()
    {
        return $this->getAdditionalProperty(self::PROPERTY_REFERENCE);
    }

    public function get_reference_object()
    {
        if (!$this->reference_object instanceof Portfolio)
        {
            $this->reference_object = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_reference()
            );
        }

        return $this->reference_object;
    }

    public function set_reference($reference)
    {
        $this->setAdditionalProperty(self::PROPERTY_REFERENCE, $reference);
    }
}
