<?php
namespace Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass
 * @author  Dieter De Neef
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Description extends ContentObject implements Versionable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Description';
}
