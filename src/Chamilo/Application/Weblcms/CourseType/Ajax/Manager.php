<?php
namespace Chamilo\Application\Weblcms\CourseType\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Application\Weblcms\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const CONTEXT = __NAMESPACE__;
}
