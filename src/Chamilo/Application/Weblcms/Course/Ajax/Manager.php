<?php
namespace Chamilo\Application\Weblcms\Course\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Application\Weblcms\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_GET_COURSES_FOR_ELEMENT_FINDER = 'GetCoursesForElementFinder';
}
