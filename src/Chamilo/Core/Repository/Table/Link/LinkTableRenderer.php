<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class LinkTableRenderer extends DataClassListTableRenderer
{
    public const TYPE_ATTACHED_TO = 4;
    public const TYPE_ATTACHES = 5;
    public const TYPE_CHILDREN = 3;
    public const TYPE_INCLUDED_IN = 6;
    public const TYPE_INCLUDES = 7;
    public const TYPE_PARENTS = 2;
    public const TYPE_PUBLICATIONS = 1;
}
