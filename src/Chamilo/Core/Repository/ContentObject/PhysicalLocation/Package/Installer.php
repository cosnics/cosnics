<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass\PhysicalLocation;

/**
 * @package Chamilo\Core\Repository\ContentObject\PhysicalLocation\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = PhysicalLocation::CONTEXT;
}
