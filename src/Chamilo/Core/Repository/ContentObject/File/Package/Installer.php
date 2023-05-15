<?php
namespace Chamilo\Core\Repository\ContentObject\File\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;

/**
 * @package Chamilo\Core\Repository\ContentObject\File\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = File::CONTEXT;
}
