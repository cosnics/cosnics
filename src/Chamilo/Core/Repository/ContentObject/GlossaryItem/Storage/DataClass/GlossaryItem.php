<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GlossaryItem extends ContentObject implements VersionableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\GlossaryItem';
}
