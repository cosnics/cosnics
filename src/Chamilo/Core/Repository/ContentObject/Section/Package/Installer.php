<?php
namespace Chamilo\Core\Repository\ContentObject\Section\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;

/**
 * @package Chamilo\Core\Repository\ContentObject\Section\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Section::CONTEXT;
}
