<?php
namespace Chamilo\Core\Repository\ContentObject\Page\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;

/**
 * @package Chamilo\Core\Repository\ContentObject\Page\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Page::CONTEXT;
}
