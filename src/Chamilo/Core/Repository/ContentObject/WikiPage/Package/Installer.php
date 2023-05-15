<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\WikiPage;

/**
 * @package Chamilo\Core\Repository\ContentObject\WikiPage\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = WikiPage::CONTEXT;
}
