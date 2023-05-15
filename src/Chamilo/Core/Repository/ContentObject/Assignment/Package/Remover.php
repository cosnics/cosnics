<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectRemover;

/**
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class Remover extends ContentObjectRemover
{
    public const CONTEXT = Installer::CONTEXT;
}
