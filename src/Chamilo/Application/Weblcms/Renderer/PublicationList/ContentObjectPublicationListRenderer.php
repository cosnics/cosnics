<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\ContentObjectPublicationDescriptionRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * $Id: content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.browser
 */

/**
 * This is a generic renderer for a set of learning object publications.
 * 
 * @package application.weblcms.tool
 * @author Bart Mollet
 * @author Tim De Pauw
 */
abstract class ContentObjectPublicationListRenderer
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    // Types
    const TYPE_LIST = 'List';
    const TYPE_TABLE = 'Table';
    const TYPE_GALLERY = 'GalleryTable';
    const TYPE_SLIDESHOW = 'Slideshow';
    const TOOL_TYPE_ANNOUNCEMENT = 'Announcement';

    protected $tool_browser;

    private $parameters;

    private $actions;

    /**
     * private counter to keep track of first/last status;
     */
    protected $row_counter = 0;

    /**
     * Constructor.
     * 
     * @param $tool_browser PublicationBrowser The tool_browser to associate this list renderer with.
     * @param $parameters array The parameters to pass to the renderer.
     */
    public function __construct($tool_browser, $parameters = array())
    {
        $this->parameters = $parameters;
        $this->tool_browser = $tool_browser;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function get_actions()
    {
        return $this->actions;
    }

    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * Renders the title of the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_title($publication)
    {
        return htmlspecialchars($this->get_content_object_from_publication($publication)->get_title());
    }

    /**
     * Renders the description of the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_description($publication)
    {
        $content_object_publication_description_renderer = new ContentObjectPublicationDescriptionRenderer(
            $this, 
            $publication);
        
        return $content_object_publication_description_renderer->render();
    }

    /**
     * Renders information about the repo_viewer of the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_repository_viewer($publication)
    {
        $user = $this->tool_browser->get_parent()->get_user_info(
            $publication[ContentObjectPublication::PROPERTY_PUBLISHER_ID]);
        
        if ($user)
        {
            return $user->get_fullname();
        }
        else
        {
            return Translation::get('UserUnknown');
        }
    }

    /**
     * Renders the date when the given publication was published.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_publication_date($publication)
    {
        return $this->format_date($publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE]);
    }

    /**
     * Renders the users and course_groups the given publication was published for.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_publication_targets($publication)
    {
        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, 
                Manager::context(), 
                $publication[ContentObjectPublication::PROPERTY_ID], 
                WeblcmsRights::TYPE_PUBLICATION, 
                $this->get_course_id(), 
                WeblcmsRights::TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }
        
        return WeblcmsRights::getInstance()->render_target_entities_as_string($target_entities);
    }

    /**
     * Renders the time period in which the given publication is active.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_publication_period($publication)
    {
        if ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
             $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
        {
            return htmlentities(Translation::get('Forever', null, Utilities::COMMON_LIBRARIES));
        }
        
        return htmlentities(
            Translation::get(
                'VisibleFromUntil', 
                array(
                    'FROM' => $this->format_date($publication[ContentObjectPublication::PROPERTY_FROM_DATE]), 
                    'UNTIL' => $this->format_date($publication[ContentObjectPublication::PROPERTY_TO_DATE])), 
                Utilities::COMMON_LIBRARIES));
    }

    /**
     * Renders general publication information about the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_publication_information($publication)
    {
        if ($publication[ContentObjectPublication::PROPERTY_EMAIL_SENT])
        {
            $email_icon = ' - <img src="' . Theme::getInstance()->getCommonImagePath('Action/Email') . '" alt=""' .
                 'style="vertical-align: middle;" title="' . Translation::get('SentByEmail') . '"/>';
        }
        
        $html = array();
        $html[] = htmlentities(Translation::get('PublishedOn', null, Utilities::COMMON_LIBRARIES)) . ' ' .
             $this->render_publication_date($publication);
        $html[] = htmlentities(Translation::get('By', null, Utilities::COMMON_LIBRARIES)) . ' ' .
             $this->render_repository_viewer($publication);
        $html[] = htmlentities(Translation::get('For', null, Utilities::COMMON_LIBRARIES)) . ' ' .
             $this->render_publication_targets($publication) . $email_icon;
        
        if ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] != 0 ||
             $publication[ContentObjectPublication::PROPERTY_TO_DATE] != 0)
        {
            $html[] = '(' . $this->render_publication_period($publication) . ')';
        }
        
        $publication_modified = $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE] >
             $publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE];
        $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
        
        $content_object_modified = $content_object->get_modification_date() >
             $publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE];
        
        if ($publication_modified || $content_object_modified)
        {
            $html[] = '<br />';
            $html[] = '<span class="highlight">';
            $html[] = htmlentities(Translation::get('LastModifiedOn'));
            
            if ($content_object_modified && $publication_modified)
            {
                if ($content_object->get_modification_date() >
                     $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE])
                {
                    $html[] = $this->format_date($content_object->get_modification_date());
                }
                else
                {
                    $html[] = $this->format_date($publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]);
                }
            }
            elseif ($content_object_modified)
            {
                $html[] = $this->format_date($content_object->get_modification_date());
            }
            else
            {
                $html[] = $this->format_date($publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]);
            }
            
            $html[] = '</span> ';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the means to move the given publication up one place.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @param $first boolean True if the publication is the first in the list it is a part of.
     * @return string The HTML rendering.
     */
    public function render_up_action($publication, $first = false)
    {
        if (! $first)
        {
            $up_img = 'Action/Up';
            $up_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_UP, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]), 
                array(), 
                true);
            $up_link = '<a href="' . $up_url . '"><img src="' . Theme::getInstance()->getCommonImagePath($up_img) .
                 '" alt=""/></a>';
        }
        else
        {
            $up_link = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/UpNa') . '"  alt=""/>';
        }
        
        return $up_link;
    }

    /**
     * Renders the means to move the given publication down one place.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @param $last boolean True if the publication is the last in the list it is a part of.
     * @return string The HTML rendering.
     */
    public function render_down_action($publication, $last = false)
    {
        if (! $last)
        {
            $down_img = 'Action/Down';
            $down_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_DOWN, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]), 
                array(), 
                true);
            $down_link = '<a href="' . $down_url . '"><img src="' . Theme::getInstance()->getCommonImagePath($down_img) .
                 '"  alt=""/></a>';
        }
        else
        {
            $down_link = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/DownNa') . '"  alt=""/>';
        }
        
        return $down_link;
    }

    /**
     * Renders the means to toggle visibility for the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_visibility_action($publication)
    {
        $visibility_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]), 
            array(), 
            true);
        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            $visibility_img = 'Action/Invisible';
        }
        
        elseif ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
             $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
        {
            $visibility_img = 'Action/Visible';
        }
        else
        {
            $visibility_img = 'Action/Period';
            $visibility_url = 'javascript:void(0)';
        }
        $visibility_link = '<a href="' . $visibility_url . '"><img src="' . Theme::getInstance()->getCommonImagePath(
            $visibility_img) . '"  alt=""/></a>';
        
        return $visibility_link;
    }

    /**
     * Renders the means to edit the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_edit_action($publication)
    {
        $edit_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]), 
            array(), 
            true);
        $edit_link = '<a href="' . $edit_url . '"><img src="' . Theme::getInstance()->getCommonImagePath('Action/Edit') .
             '"  alt=""/></a>';
        
        return $edit_link;
    }

    public function render_top_action($publication)
    {
        return '<a href="#top"><img src="' . Theme::getInstance()->getCommonImagePath('Action/AjaxAdd') .
             '"  alt=""/></a>';
    }

    /**
     * Renders the means to delete the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_delete_action($publication)
    {
        $delete_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]), 
            array(), 
            true);
        $delete_link = '<a href="' . $delete_url . '" onclick="return confirm(\'' .
             addslashes(htmlentities(Translation::get('ConfirmYourChoice'))) . '\');"><img src="' .
             Theme::getInstance()->getCommonImagePath('Action/Delete') . '"  alt=""/></a>';
        
        return $delete_link;
    }

    /**
     * Renders the means to give feedback to the given publication
     * 
     * @param $publication ContentObjectPublication The publication
     */
    public function render_feedback_action($publication)
    {
        $feedback_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID], 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => 'view'), 
            array(), 
            true);
        $feedback_link = '<a href="' . $feedback_url . '"><img src="' . Theme::getInstance()->getCommonImagePath(
            'Action/Browser') . '" alt=""/></a>';
        
        return $feedback_link;
    }

    /**
     * Renders the means to move the given publication to another category.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The HTML rendering.
     */
    public function render_move_to_category_action($publication)
    {
        if ($this->get_tool_browser() instanceof Categorizable)
        {
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class_name(), 
                    ContentObjectPublicationCategory::PROPERTY_COURSE), 
                new StaticConditionVariable($this->tool_browser->get_parent()->get_course_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class_name(), 
                    ContentObjectPublicationCategory::PROPERTY_TOOL), 
                new StaticConditionVariable($this->tool_browser->get_parent()->get_tool_id()));
            
            $count = DataManager::count(ContentObjectPublicationCategory::class_name(), new AndCondition($conditions));
            
            $count ++;
            if ($count > 1)
            {
                $url = $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]), 
                    array(), 
                    true);
                $link = '<a href="' . $url . '"><img src="' . Theme::getInstance()->getCommonImagePath('Action/Move') .
                     '"  alt=""/></a>';
            }
            else
            {
                $link = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/MoveNa') . '"  alt=""/>';
            }
            
            return $link;
        }
        else
        {
            return null;
        }
    }

    /**
     * Renders the attachements of a publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The rendered HTML.
     */
    public function render_attachments($publication)
    {
        $object = $this->get_content_object_from_publication($publication);
        if ($object instanceof AttachmentSupport)
        {
            $attachments = $object->get_attachments();
            if (count($attachments) > 0)
            {
                $html[] = '<h4>Attachments</h4>';
                Utilities::order_content_objects_by_title($attachments);
                $html[] = '<ul>';
                foreach ($attachments as $attachment)
                {
                    $html[] = '<li><a href="' . $this->tool_browser->get_url(
                        array(
                            Manager::PARAM_PUBLICATION => $publication[ContentObjectPublication::PROPERTY_ID], 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW_ATTACHMENT, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_OBJECT_ID => $attachment->get_id())) .
                         '"><img src="' . $attachment->get_icon_path(Theme::ICON_MINI) . '" alt="' . htmlentities(
                            Translation::get(ContentObject::type_to_class($attachment->get_type()) . 'TypeName')) . '"/> ' .
                         $attachment->get_title() . '</a></li>';
                }
                $html[] = '</ul>';
                
                return implode(PHP_EOL, $html);
            }
        }
        
        return '';
    }

    /**
     * Renders publication actions for the given publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @param $first boolean True if the publication is the first in the list it is a part of.
     * @param $last boolean True if the publication is the last in the list it is a part of.
     * @return string The rendered HTML.
     */
    public function render_publication_actions($publication, $first, $last)
    {
        $html = array();
        $html[] = $this->get_publication_actions($publication)->as_html();
        
        return implode($html);
    }

    /**
     * Renders the icon for the given publication
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The rendered HTML.
     */
    public function render_icon($publication)
    {
        return $this->get_content_object_from_publication($publication)->get_icon_image();
    }

    /**
     * Returns the content object for a given publication
     * 
     * @param mixed[] $publication
     *
     * @return ContentObject
     */
    public function get_content_object_from_publication($publication)
    {
        $class = $publication[ContentObject::PROPERTY_TYPE];
        $content_object = new $class($publication);
        $content_object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
        
        return $content_object;
    }

    /**
     * Checks if a publication is visible for target users
     * 
     * @param $publication
     * @return bool
     */
    public function is_visible_for_target_users($publication)
    {
        return (! $publication[ContentObjectPublication::PROPERTY_HIDDEN] && ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] ==
             0 && $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0) || ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] <=
             time() && $publication[ContentObjectPublication::PROPERTY_TO_DATE] >= time()));
    }

    /**
     * Formats the given date in a human-readable format.
     * 
     * @param $date int A UNIX timestamp.
     * @return string The formatted date.
     */
    public function format_date($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
        
        return DatetimeUtilities::format_locale_date($date_format, $date);
    }

    /**
     *
     * @see ContentObjectPublicationBrowser :: get_publications()
     */
    public function get_publications($offset = 0, $max_objects = - 1, OrderBy $object_table_order = null)
    {
        if (! $object_table_order)
        {
            $object_table_order = $this->tool_browser->get_default_order_property();
        }
        
        return $this->tool_browser->get_publications($offset, $max_objects, $object_table_order);
    }

    /**
     *
     * @see ContentObjectPublicationBrowser :: get_publication_count()
     */
    public function get_publication_count()
    {
        return $this->tool_browser->get_publication_count();
    }

    /**
     * Returns the value of the given renderer parameter.
     * 
     * @param $name string The name of the parameter.
     * @return mixed The value of the parameter.
     */
    public function get_parameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Sets the value of the given renderer parameter.
     * 
     * @param $name string The name of the parameter.
     * @param $value mixed The new value for the parameter.
     */
    public function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Returns the output of the list renderer as HTML.
     * 
     * @return string The HTML.
     */
    abstract public function as_html();

    /**
     *
     * @see ContentObjectPublicationBrowser :: get_url()
     */
    public function get_url($parameters = array(), $filter = array(), $encode_entities = false)
    {
        return $this->tool_browser->get_url($parameters, $filter, $encode_entities);
    }

    public function get_complex_builder_url($publication_id)
    {
        return $this->tool_browser->get_complex_builder_url($publication_id);
    }

    public function get_complex_display_url($publication_id)
    {
        return $this->tool_browser->get_complex_display_url($publication_id);
    }

    /**
     *
     * @see ContentObjectPublicationBrowser :: is_allowed()
     */
    public function is_allowed($right, $publication = null)
    {
        return $this->tool_browser->is_allowed($right, $publication);
    }

    public static function factory($type, $tool_browser)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'ContentObjectPublicationListRenderer';
        
        if (! class_exists($class))
        {
            throw new Exception(
                Translation::get('ContentObjectPublicationListRendererTypeDoesNotExist', array('type' => $type)));
        }
        
        return new $class($tool_browser);
    }

    public function get_tool_browser()
    {
        return $this->tool_browser;
    }

    public function get_allowed_types()
    {
        return $this->tool_browser->get_allowed_types();
    }

    public function get_search_condition()
    {
        return $this->tool_browser->get_search_condition();
    }

    public function get_publication_conditions()
    {
        return $this->tool_browser->get_publication_conditions();
    }

    public function get_user()
    {
        $va_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);
        
        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                return $this->tool_browser->get_user_info($va_id);
            }
        }
        
        return $this->tool_browser->get_user();
    }

    public function get_user_id()
    {
        $va_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);
        
        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                return $this->tool_browser->get_user_info($va_id)->get_id();
            }
        }
        
        return $this->tool_browser->get_user_id();
    }

    public function get_course_id()
    {
        return $this->tool_browser->get_course_id();
    }

    public function get_tool_id()
    {
        return $this->tool_browser->get_tool_id();
    }

    public function get_publication_type()
    {
        if ($this->tool_browser instanceof \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent)
        {
            return $this->tool_browser->get_publication_type();
        }
        else
        {
            return 0;
        }
    }

    public function get_publication_actions($publication, $show_move = true, $ascending = true)
    {
        $has_edit_right = $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication);
        
        $publication_id = $publication[ContentObjectPublication::PROPERTY_ID];
        $publication_type = $this->get_publication_type();
        
        $content_object = $this->get_content_object_from_publication($publication);
        
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        // quick-win: (re)send mail after publication
        // currently only mail button for announcements; this outer check can be removed, but then all
        // tools must have a <ToolName>PublicationMailerComponent class
        // (see: application/weblcms/tool/announcement/php/lib/component/publication_mailer.class.php)
        if ($has_edit_right &&
             $publication[ContentObjectPublicationCategory::PROPERTY_TOOL] == self::TOOL_TYPE_ANNOUNCEMENT)
        {
            
            if (! $publication[ContentObjectPublication::PROPERTY_EMAIL_SENT] &&
             ! $publication[ContentObjectPublication::PROPERTY_HIDDEN])
        // && RightsUtilities :: is_allowed(EmailRights :: MAIL_ALLOWED, EmailRights :: LOCATION, EmailRights ::
        // TYPE))
        {
            $email_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MAIL_PUBLICATION));
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('SendByEMail'), 
                    Theme::getInstance()->getCommonImagePath('Action/Email'), 
                    $email_url, 
                    ToolbarItem::DISPLAY_ICON, 
                    true));
        }
    }
    
    $details_url = $this->get_url(
        array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW));
    $toolbar->add_item(
        new ToolbarItem(
            Translation::get('Details', null, Utilities::COMMON_LIBRARIES), 
            Theme::getInstance()->getCommonImagePath('Action/Details'), 
            $details_url, 
            ToolbarItem::DISPLAY_ICON));
    
    if ($content_object instanceof ComplexContentObjectSupport)
    {
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('DisplayComplex'), 
                Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                $this->get_complex_display_url($publication_id), 
                ToolbarItem::DISPLAY_ICON));
    }
    
    $repositoryRightsService = \Chamilo\Core\Repository\Workspace\Service\RightsService::getInstance();
    $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();
    
    $canEditContentObject = $repositoryRightsService->canEditContentObject($this->get_user(), $content_object);
    $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublication(
        $this->get_user(), 
        new ContentObjectPublication($publication), 
        $this->tool_browser->get_application()->get_course());
    
    if ($canEditContentObject || $canEditPublicationContentObject)
    {
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('EditContentObject', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_CONTENT_OBJECT, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                ToolbarItem::DISPLAY_ICON));
        
        if ($content_object instanceof ComplexContentObjectSupport)
        {
            if (\Chamilo\Core\Repository\Builder\Manager::exists($content_object->package()))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('BuildComplexObject', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/Build'), 
                        $this->get_complex_builder_url($publication_id), 
                        ToolbarItem::DISPLAY_ICON));
                
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Preview', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/Preview'), 
                        $this->get_complex_display_url($publication_id), 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('BuildPreview', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/BuildPreview'), 
                        $this->get_complex_display_url($publication_id), 
                        ToolbarItem::DISPLAY_ICON));
            }
        }
    }
    
    if ($has_edit_right)
    {
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('EditPublicationDetails', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Action/EditPublication'), 
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                ToolbarItem::DISPLAY_ICON));
        
        // quick-win: correct implementation of moving up and down.
        // Move publications up and down with the arrow buttons.
        // assuming this methods gets called only once per row in the table:
        // update the row counter;
        //
        // The meaning of up and down depends on whether the list is sorted in ascending
        // or descending order, this is determined with the $ascending parameter for this method
        // (ugly parameter passing, but not less ugly than the $show_mvoe already present)
        // When the $ascending parameter is omitted, this code works just as before.
        //
        // TODO: refactor this code out of this method as much as possible.
        ++ $this->row_counter;
        $first_row = $this->row_counter == 1;
        $last_row = $this->row_counter == $this->get_publication_count();
        
        $true_up = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION_UP;
        $true_down = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION_DOWN;
        
        if ($show_move)
        {
            if (! $first_row)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/Up'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => $true_up, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type)), 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveUpNA', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/UpNa'), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
            
            if (! $last_row)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/Down'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => $true_down, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type)), 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveDownNA', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/DownNa'), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
        }
        
        $visibility_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type));
        
        // New functionality in old code
        
        if ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
             $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
        {
            $variable = 'PeriodForever';
            $visibility_image = 'Action/Period';
        }
        else
        {
            if (time() < $publication[ContentObjectPublication::PROPERTY_FROM_DATE])
            {
                $variable = 'PeriodBefore';
                $visibility_image = 'Action/PeriodBefore';
            }
            elseif (time() > $publication[ContentObjectPublication::PROPERTY_TO_DATE])
            {
                $variable = 'PeriodAfter';
                $visibility_image = 'Action/PeriodAfter';
            }
            else
            {
                $variable = 'PeriodCurrent';
                $visibility_image = 'Action/Period';
            }
        }
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get($variable, null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath($visibility_image), 
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                ToolbarItem::DISPLAY_ICON));
        
        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            $visibility_image = 'Action/Invisible';
            $visibilityTranslation = Translation::get('MakeVisible', null, Manager::context());
        }
        else
        {
            $visibilityTranslation = Translation::get('MakeInvisible', null, Manager::context());
            $visibility_image = 'Action/Visible';
        }
        
        $toolbar->add_item(
            new ToolbarItem(
                $visibilityTranslation, 
                Theme::getInstance()->getCommonImagePath($visibility_image), 
                $visibility_url, 
                ToolbarItem::DISPLAY_ICON));
        
        // Move the publication
        if ($this->get_tool_browser()->get_parent() instanceof Categorizable &&
             $this->get_tool_browser()->hasCategories())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveToCategory', null, Manager::context()), 
                    Theme::getInstance()->getCommonImagePath('Action/Move'), 
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                    ToolbarItem::DISPLAY_ICON));
        }
    }
    $course = $this->get_tool_browser()->get_course();
    if ($course->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin())
    {
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ManageRights', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Rights'), 
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                ToolbarItem::DISPLAY_ICON));
    }
    
    if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
    {
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                ToolbarItem::DISPLAY_ICON, 
                true, 
                null, 
                null, 
                Translation::get('ConfirmDeletePublication', null, 'Chamilo\Application\Weblcms')));
    }
    
    if (method_exists($this->get_tool_browser()->get_parent(), 'add_content_object_publication_actions'))
    {
        // $content_object_publication_actions =
        $this->get_tool_browser()->get_parent()->add_content_object_publication_actions($toolbar, $publication);
    }
    
    return $toolbar;
}

/**
 *
 * @return string
 */
public static function package()
{
    return static::context();
}
}
