<?php
namespace Chamilo\Core\Tracking\Component;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\Tracking\Table\Event\EventTable;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ConditionProperty;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: admin_event_browser.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Component for viewing tracker events
 */
class AdminEventBrowserComponent extends Manager implements TableSupport
{

    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->action_bar = $this->get_action_bar();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();

        $isactive = (PlatformSetting :: get('enable_tracking', 'core\tracking') == 1);

        if ($isactive)
        {
            $html[] = $this->get_user_html();
        }
        else
        {
            $html[] = $this->display_error_message(
                '<a href="' . $this->get_url(array(self :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context())) .
                     '">' . Translation :: get('Tracking_is_disabled') . '</a>');
        }

        $html[] = $this->render_footer();

        return implode("\n", $html);
    }

    public function get_user_html()
    {
        $table = new EventTable($this);

        $html = array();
        $html[] = '<div>';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    public function get_condition()
    {
        return $this->action_bar->get_conditions(array(new ConditionProperty(Event :: PROPERTY_NAME)));
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_browser.png',
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('tracking_event_browser');
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        $this->get_condition();
    }
}
