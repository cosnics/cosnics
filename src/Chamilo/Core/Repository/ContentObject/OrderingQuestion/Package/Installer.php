<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\OrderingQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = OrderingQuestion::CONTEXT;
}
