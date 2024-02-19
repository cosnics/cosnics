<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.portfolio_item
 */
class ComplexPortfolioItem extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = PortfolioItem::CONTEXT;
}
