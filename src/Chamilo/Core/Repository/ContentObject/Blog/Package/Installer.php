<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog;

/**
 * @package Chamilo\Core\Repository\ContentObject\Blog\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Blog::CONTEXT;
}
