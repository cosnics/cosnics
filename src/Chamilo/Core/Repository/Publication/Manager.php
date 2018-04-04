<?php
namespace Chamilo\Core\Repository\Publication;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'publication_action';
    const PARAM_PUBLICATION_APPLICATION = 'publication_application';
    const PARAM_PUBLICATION_ID = 'publication';
    const PARAM_PUBLICATION_CONTEXT = 'publication_context';
    
    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_PUBLISH = 'Publisher';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    
    // Properties
    const WIZARD_LOCATION = 'location';
    const WIZARD_OPTION = 'option';
}
