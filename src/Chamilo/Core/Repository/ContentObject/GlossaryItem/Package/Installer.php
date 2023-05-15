<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;

/**
 * @package Chamilo\Core\Repository\ContentObject\GlossaryItem\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = GlossaryItem::CONTEXT;
}
