<?php
namespace Chamilo\Libraries\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Libraries\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_RESOURCE = 'Resource';
    const DEFAULT_ACTION = self::ACTION_RESOURCE;
    const PARAM_MODIFIED = 'modified';
}
