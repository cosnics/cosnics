<?php
namespace Chamilo\Core\Repository\Publication;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package core\repository\user_view
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_PUBLISH = 'Publisher';
    public const ACTION_UPDATE = 'Updater';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'publication_action';
    public const PARAM_PUBLICATION_APPLICATION = 'publication_application';
    public const PARAM_PUBLICATION_CONTEXT = 'publication_context';
    public const PARAM_PUBLICATION_ID = 'publication';

    public const WIZARD_OPTION = 'option';
    public const WIZARD_TARGET = 'target';
}
