<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = LearningPath::CONTEXT;
}
