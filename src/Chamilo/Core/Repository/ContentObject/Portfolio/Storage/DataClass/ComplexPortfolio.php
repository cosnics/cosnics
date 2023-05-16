<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * Portfolio complex content object item
 *
 * @package repository\content_object\portfolio$ComplexPortfolio
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ComplexPortfolio extends ComplexContentObjectItem
{
    public const CONTEXT = Portfolio::CONTEXT;

    public function get_allowed_types(): array
    {
        return [Portfolio::class, PortfolioItem::class];
    }
}
