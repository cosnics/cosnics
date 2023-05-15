<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;

/**
 * @package Chamilo\Core\Repository\ContentObject\ForumTopic\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = ForumTopic::CONTEXT;
}
