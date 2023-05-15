<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Package
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Assignment::CONTEXT;
}
