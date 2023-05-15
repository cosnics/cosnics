<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;

/**
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Portfolio::CONTEXT;
}
