<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;

/**
 * @package Chamilo\Core\Repository\ContentObject\RssFeed\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = RssFeed::CONTEXT;
}
