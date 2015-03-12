<?php
namespace Chamilo\Core\Home;

use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package common.libraries
 */
class BlockRendition
{
    const PARAM_ACTION = 'block_action';
    const BLOCK_LIST_SIMPLE = 'simple';
    const BLOCK_LIST_ADVANCED = 'advanced';

    // display the block view for integration into chamil's home page
    const BLOCK_VIEW = 'block_view';
    // display the widget view for integration into a third party application such as a portal
    const WIDGET_VIEW = 'widget_view';
    const BLOCK_PROPERTY_ID = 'id';
    const BLOCK_PROPERTY_NAME = 'name';
    const BLOCK_PROPERTY_IMAGE = 'image';

    private $parent;

    private $type;

    private $block_info;

    private $configuration;

    private $view = self :: BLOCK_VIEW;

    /**
     *
     * @param Renderer $renderer
     * @param Block $block_info
     * @return Block
     */
    public static function factory(Renderer $renderer, Block $block_info, $configuration = null)
    {
        $class = $block_info->get_context() . '\Type\\' .
             (string) StringUtilities :: getInstance()->createString($block_info->get_component())->upperCamelize();

        return new $class($renderer, $block_info, $configuration);
    }

    /**
     * Returns the default image to be displayed for block's creation.
     * Can be redefined in subclasses to change the
     * default icon.
     *
     * @todo : not so good. would be better to make the whole "create block" a view.
     * @param string $application
     * @param string $type
     * @param string $size
     * @return string
     */
    public static function get_image_path($context, $type)
    {
        $class = $context . '\Type\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize();

        if (method_exists($class, 'get_default_image_path'))
        {
            return $class :: get_default_image_path();
        }
        else
        {
            $image_path = Theme :: getInstance()->getImagePath($context, 'Blocks/' . $type, 'png', false);

            if (! file_exists($image_path) || ! is_file($image_path))
            {
                return Theme :: getInstance()->getImagePath(
                    ClassnameUtilities :: getInstance()->getNamespaceParent($context, 3),
                    'Logo/' . Theme :: ICON_MEDIUM);
            }
            else
            {
                return Theme :: getInstance()->getImagePath($context, 'Blocks/' . $type);
            }
        }
    }

    public function __construct($parent, $block_info, $configuration = null)
    {
        $this->parent = $parent;
        $this->block_info = $block_info;
        $this->configuration = $configuration ? $configuration : $block_info->get_configuration();
    }

    /**
     * The type of view: block or widget.
     * Block - default - is for integration into Chamilo's homepage. Widget is for
     * integration into a third party application - i.e. external portal.
     *
     * @return string
     */
    public function get_view()
    {
        return $this->view;
    }

    public function set_view($view)
    {
        $this->view = $view;
    }

    /**
     * Returns the tool which created this publisher.
     *
     * @return Tool The tool.
     */
    public function get_parent()
    {
        return $this->parent;
    }

    public function get_configuration()
    {
        return $this->configuration;
    }

    /**
     *
     * @see Tool::get_user_id()
     */
    public function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    public function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * Returns the types of learning object that this object may publish.
     *
     * @return array The types.
     */
    public function get_type()
    {
        return $this->type;
    }

    public function get_block_info()
    {
        return $this->block_info;
    }

    public function is_editable()
    {
        return true;
    }

    public function is_hidable()
    {
        return true;
    }

    public function is_deletable()
    {
        return true;
    }

    /**
     * Returns true if the block is to be displayed, false otherwise.
     * By default do not show on home page when user is
     * not connected.
     *
     * @return bool
     */
    public function is_visible()
    {
        return Session :: get_user_id() != 0;
    }

    /**
     * Returns the block's title to display.
     *
     * @return string
     */
    public function get_title()
    {
        return $this->get_block_info()->get_title();
    }

    /**
     * Link target for external links.
     * I.e. links that do not modify the widget itself. In widget mode they should point
     * to a new windows.
     */
    public function get_link_target()
    {
        return '';
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    public function get_icon()
    {
        $context = ClassnameUtilities :: getInstance()->getNamespaceParent($this->get_block_info()->get_context(), 4);
        return Theme :: getInstance()->getImagePath($context, 'Logo/' . Theme :: ICON_MEDIUM);
    }

    public function as_html($view = '')
    {
        if (! $this->is_visible())
        {
            return '';
        }
        if ($view)
        {
            $this->set_view($view);
        }

        $config = $this->get_configuration();

        $html = array();
        $html[] = $this->render_header();
        $html[] = $this->display_content();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_header()
    {
        $block_id = $this->get_block_info()->get_id();
        $icon_url = $this->get_icon();

        $title = $this->display_title();
        if ($this->get_view() == self :: BLOCK_VIEW)
        { // i.e. in widget view it is the portal configuration that decides to show/hide
            $description_style = $this->get_block_info()->is_visible() ? '' : ' style="display: none"';
        }
        else
        {
            $description_style = '';
        }

        $html = array();
        $html[] = '<div class="portal_block" id="portal_block_' . $block_id . '" style="background-image: url(' .
             $icon_url . ');">';
        $html[] = $title;
        $html[] = '<div class="entry-content description"' . $description_style . '>';

        return implode(PHP_EOL, $html);
    }

    public function display_title()
    {
        $title = htmlspecialchars($this->get_title());
        $actions = $this->display_actions();

        $html = array();
        $html[] = '<div class="title"><div style="float: left;" class="entry-title">' . $title . '</div>';
        $html[] = $actions;
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function display_actions()
    {
        if ($this->get_view() != self :: BLOCK_VIEW)
        {
            return '';
        }

        $html = array();

        $user_home_allowed = PlatformSetting :: get('allow_user_home', Manager :: context());

        if ($this->get_user() instanceof User && ($user_home_allowed || $this->get_user()->is_platform_admin()) &&
             ! $this->get_user()->is_anonymous_user())
        {
            if ($this->is_deletable())
            {
                $delete_text = Translation :: get('Delete');
                $html[] = '<a href="' . htmlspecialchars($this->get_block_deleting_link($this->get_block_info())) .
                     '" class="deleteEl" title="' . $delete_text . '">';
                $html[] = '<img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Delete')) .
                     '" alt="' . $delete_text . '" title="' . $delete_text . '"/></a>';
            }

            if ($this->block_info->is_configurable())
            {
                $configure_text = Translation :: get('Configure');
                $html[] = '<a href="' . htmlspecialchars($this->get_block_configuring_link($this->get_block_info())) .
                     '" class="configEl" title="' . $configure_text . '">';
                $html[] = '<img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Config')) .
                     '" alt="' . $configure_text . '" title="' . $configure_text . '"/></a>';
            }

            if ($this->is_editable())
            {
                $edit_text = Translation :: get('Edit');
                $html[] = '<a href="' . htmlspecialchars($this->get_block_editing_link($this->get_block_info())) .
                     '" class="editEl" title="' . $edit_text . '">';
                $html[] = '<img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Edit')) .
                     '" alt="' . $edit_text . '" title="' . $edit_text . '"/></a>';
            }

            if ($this->is_hidable())
            {
                $toggle_visibility_text = Translation :: get('ToggleVisibility');
                $html[] = '<a href="' . htmlspecialchars($this->get_block_visibility_link($this->get_block_info())) .
                     '" class="closeEl" title="' . $toggle_visibility_text . '">';
                $html[] = '<img class="visible"' .
                     ($this->get_block_info()->is_visible() ? '' : ' style="display: none;"') . ' src="' .
                     htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Visible')) . '" alt="' .
                     $toggle_visibility_text . '" title="' . $toggle_visibility_text . '"/>';
                $html[] = '<img class="invisible"' .
                     ($this->get_block_info()->is_visible() ? ' style="display: none;"' : '') . ' src="' .
                     htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Invisible')) . '" alt="' .
                     $toggle_visibility_text . '" title="' . $toggle_visibility_text . '"/></a>';
            }

            $drag_text = Translation :: get('Drag');
            $html[] = '<a href="#" id="drag_block_' . $this->get_block_info()->get_id() . '" class="dragEl" title="' .
                 $drag_text . '">';
            $html[] = '<img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Drag')) .
                 '" alt="' . $drag_text . '" title="' . $drag_text . '"/></a>';
        }

        return implode(PHP_EOL, $html);
    }

    public function display_content()
    {
        return '';
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_block_visibility_link($home_block)
    {
        return $this->get_manipulation_link(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_EDIT_HOME,
                Manager :: PARAM_HOME_TYPE => Manager :: TYPE_BLOCK,
                Manager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    public function get_block_deleting_link($home_block)
    {
        return '#';
    }

    public function get_block_editing_link($home_block)
    {
        return $this->get_manipulation_link(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_EDIT_HOME_PERSONAL,
                Manager :: PARAM_HOME_TYPE => Manager :: TYPE_BLOCK,
                Manager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    public function get_block_configuring_link($home_block)
    {
        return $this->get_manipulation_link(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_CONFIGURE_HOME_PERSONAL,
                Manager :: PARAM_HOME_TYPE => Manager :: TYPE_BLOCK,
                Manager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    public function get_manipulation_link($parameters)
    {
        return Redirect :: get_link($parameters, array(), false);
    }

    public function get_link($parameters = array(), $encode = false)
    {
        return Redirect :: get_link($parameters, array(), $encode);
    }

    public function get_url($parameters = array(), $filter = array(), $encode_entities = false)
    {
        if ($widget_id = Request :: get(Renderer :: PARAM_WIDGET_ID))
        {
            $parameters[Renderer :: PARAM_WIDGET_ID] = $widget_id;
        }
        $result = $this->get_parent()->get_url($parameters, $filter, $encode_entities);
        return $result;
    }

    public function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    public function get_parameters()
    {
        $result = $this->get_parent()->get_parameters();
        if ($widget_id = Request :: get(Renderer :: PARAM_WIDGET_ID))
        {
            $result[Renderer :: PARAM_WIDGET_ID] = $widget_id;
        }

        return $result;
    }

    /**
     * Default response for blocks who use an attachment viewer.
     * Override for different functionality.
     *
     * @param ContentObject $object The content object to be tested.
     * @return boolean default response: false.
     */
    public function is_view_attachment_allowed($object)
    {
        return false;
    }
}
