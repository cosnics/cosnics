<?php
namespace Chamilo\Core\Group\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Core\Group\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_XML_GROUP_MENU_FEED = 'XmlGroupMenuFeed';
    const ACTION_GET_GROUP_CHILDREN_JSON = 'GetGroupChildrenJSON';
}
