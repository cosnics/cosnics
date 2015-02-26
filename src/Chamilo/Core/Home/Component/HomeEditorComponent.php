<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: editor.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.home_manager.component
 */

/**
 * Repository manager component to edit an existing learning object.
 */
class HomeEditorComponent extends EditorComponent
{

    public function redirect($success)
    {
        parent :: redirect(
            Translation :: get($success ? 'HomeUpdated' : 'HomeNotUpdated'), 
            ($success ? false : true), 
            array(Application :: PARAM_ACTION => self :: ACTION_VIEW_HOME));
    }
}
