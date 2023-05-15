<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;

/**
 * @package Chamilo\Core\Repository\ContentObject\PortfolioItem\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = PortfolioItem::CONTEXT;
}
