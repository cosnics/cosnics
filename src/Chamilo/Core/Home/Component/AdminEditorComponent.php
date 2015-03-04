<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: editor.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class AdminEditorComponent extends EditorComponent
{

    public function redirect($success)
    {
        parent :: redirect(
            Translation :: get($success ? 'HomeUpdated' : 'HomeNotUpdated'), 
            ($success ? false : true), 
            array(Application :: PARAM_ACTION => self :: ACTION_MANAGE_HOME));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_MANAGE_HOME)), 
                Translation :: get('HomeManagerManagerComponent')));
        $breadcrumbtrail->add_help('home_editor');
    }

    /**
     * Returns the admin breadcrumb generator
     * 
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
