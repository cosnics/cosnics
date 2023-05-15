<?php
namespace Chamilo\Core\Repository\ContentObject\Note\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note;

/**
 * @package Chamilo\Core\Repository\ContentObject\Note\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Note::CONTEXT;
}
