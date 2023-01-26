<?php
namespace Chamilo\Core\Rights\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package    Chamilo\Core\Rights\Ajax
 * @author     Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author     Magali Gillard <magali.gillard@ehb.be>
 * @author     Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Should not be needed anymore
 */
abstract class Manager extends AjaxManager
{
    public const CONTEXT = __NAMESPACE__;
}
