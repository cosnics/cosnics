<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass
 */
class ComplexPortfolioItem extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = PortfolioItem::CONTEXT;
}
