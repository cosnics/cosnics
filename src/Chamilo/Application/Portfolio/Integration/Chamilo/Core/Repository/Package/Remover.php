<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Package;

use Chamilo\Configuration\Package\NotAllowed;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Remover extends \Chamilo\Configuration\Package\Action\Remover implements NotAllowed
{
    public const CONTEXT = Installer::CONTEXT;
}
