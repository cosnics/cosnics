<?php
namespace Chamilo\Application\CasStorage\Service;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\CasStorage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'account_action';
    const PARAM_SERVICE_ID = 'service_id';
    const ACTION_ACTIVATE = 'Activater';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_DEACTIVATE = 'Deactivater';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
