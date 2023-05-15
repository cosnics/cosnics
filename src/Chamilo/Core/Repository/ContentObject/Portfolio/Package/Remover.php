<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectRemover;

/**
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Remover extends ContentObjectRemover
{
    public const CONTEXT = Installer::CONTEXT;
}
