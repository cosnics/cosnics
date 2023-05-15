<?php
namespace Chamilo\Core\Repository\ContentObject\File\HtmlEditor\Package;

use Chamilo\Configuration\Package\NotAllowed;

/**
 * Class Remover
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class Remover extends \Chamilo\Configuration\Package\Action\Remover implements NotAllowed
{
    public const CONTEXT = Installer::CONTEXT;
}
