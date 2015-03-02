<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'viewer_action';
    const PARAM_EDIT = 'edit';
    const PARAM_ID = 'viewer_object_id';
    const PARAM_EDIT_ID = 'viewer_edit_object_id';
    const PARAM_QUERY = 'query';
    const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PARAM_CONTENT_OBJECT_TEMPLATE_REGISTRATION_ID = 'template_id';
    const PARAM_PUBLISH_SELECTED = 'viewer_selected';
    const PARAM_IMPORT_TYPE = 'import_type';
    const PARAM_IMPORTED_CONTENT_OBJECT_IDS = 'imported_content_object_ids';
    const ACTION_CREATOR = 'creator';
    const ACTION_BROWSER = 'browser';
    const ACTION_PUBLISHER = 'publisher';
    const ACTION_VIEWER = 'viewer';
    const ACTION_IMPORTER = 'importer';
    const ACTION_IMPORTED_SELECTER = 'imported_selecter';
    const DEFAULT_ACTION = self :: ACTION_CREATOR;

    /**
     *
     * @var multitype:string
     */
    private $types;

    /**
     *
     * @var multitype:string
     */
    private $actions;

    private $parameters;

    private $maximum_select;

    private $excluded_objects;

    private $tabs;

    private $creation_defaults;

    /**
     * Allow selection of multiple content objects in the viewer
     *
     * @var int
     */
    const SELECT_MULTIPLE = 0;

    /**
     * Allow selection of just one content object in the viewer
     *
     * @var int
     */
    const SELECT_SINGLE = 1;

    /**
     *
     * @param \libraries\architecture\application\Application $parent
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $parent = null)
    {
        parent :: __construct($request, $user, $parent);
        $this->maximum_select = self :: SELECT_MULTIPLE;
        $this->default_content_objects = array();
        $this->parameters = array();
        $this->excluded_objects = array();

        $this->set_actions(array(self :: ACTION_CREATOR, self :: ACTION_BROWSER, self :: ACTION_IMPORTER));
        $this->set_parameter(
            self :: PARAM_ACTION,
            (Request :: get(self :: PARAM_ACTION) ? Request :: get(self :: PARAM_ACTION) : self :: ACTION_CREATOR));
    }

    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();

        $current_action = $this->get_parameter(self :: PARAM_ACTION);

        $actions = $this->get_actions();

        if ($current_action == self :: ACTION_VIEWER)
        {
            $actions[] = self :: ACTION_VIEWER;
        }

        if ($current_action == self :: ACTION_IMPORTED_SELECTER)
        {
            $actions[] = self :: ACTION_IMPORTED_SELECTER;
        }

        $this->tabs = new DynamicVisualTabsRenderer('viewer');

        foreach ($actions as $viewer_action)
        {
            if ($current_action == $viewer_action)
            {
                $selected = true;
            }
            elseif ($current_action == self :: ACTION_PUBLISHER && $viewer_action == self :: ACTION_CREATOR)
            {
                $selected = true;
            }
            else
            {
                $selected = false;
            }

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = $viewer_action;

            if ($viewer_action == self :: ACTION_VIEWER)
            {
                $parameters[self :: PARAM_ID] = Request :: get(self :: PARAM_ID);
            }

            $label = Translation :: get(
                (string) StringUtilities :: getInstance()->createString($viewer_action)->upperCamelize() . 'Title');
            $link = $this->get_url($parameters);
            $this->tabs->add_tab(
                new DynamicVisualTab(
                    $viewer_action,
                    $label,
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . $viewer_action),
                    $link,
                    $selected));
        }

        $html[] = $this->tabs->header();
        $html[] = $this->tabs->body_header();

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param int $maximum_select
     */
    public function set_maximum_select($maximum_select)
    {
        $this->maximum_select = $maximum_select;
    }

    /**
     *
     * @return int
     */
    public function get_maximum_select()
    {
        return $this->maximum_select;
    }

    /**
     * Returns the types of content object that the viewer can use.
     *
     * @return multitype:string
     */
    public function get_types()
    {
        return $this->get_application()->get_allowed_content_object_types();
    }

    /**
     *
     * @return multitype:string
     */
    public function get_actions()
    {
        return $this->actions;
    }

    /**
     *
     * @param multitype:string $actions
     */
    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     *
     * @return multitype:int
     */
    public function get_excluded_objects()
    {
        return $this->excluded_objects;
    }

    /**
     *
     * @param multitype:int $excluded_objects
     */
    public function set_excluded_objects($excluded_objects)
    {
        $this->excluded_objects = $excluded_objects;
    }

    /**
     *
     * @return boolean
     */
    public function any_object_selected()
    {
        $object = Request :: get(self :: PARAM_ID);
        return isset($object);
    }

    /**
     *
     * @return Ambigous <multitype:int, int>
     */
    public static function get_selected_objects()
    {
        return Request :: get(self :: PARAM_ID);
    }

    /**
     *
     * @return boolean
     */
    public static function is_ready_to_be_published()
    {
        $action = Request :: get(self :: PARAM_ACTION);

        $table_name = Request :: post('table_name');
        $table_parameters = unserialize(base64_decode(Request :: post($table_name . '_action_value')));

        if (isset($table_parameters[self :: PARAM_ACTION]))
        {
            $action = $table_parameters[self :: PARAM_ACTION];
        }

        return (self :: any_object_selected() && $action == self :: ACTION_PUBLISHER);
    }
}
