<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPathItem\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = LearningPathItem::CONTEXT;
}
