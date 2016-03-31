<?php
namespace Chamilo\Core\Repository\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Core\Repository\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_CATEGORY_MENU_FEED = 'XmlRepositoryCategoryMenuFeed';
    const ACTION_THUMBNAIL = 'Thumbnail';
    const ACTION_IMPORT_FILE = 'ImportFile';
    const ACTION_DELETE_FILE = 'DeleteFile';
}
