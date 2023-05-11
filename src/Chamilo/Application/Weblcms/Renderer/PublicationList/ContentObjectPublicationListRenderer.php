<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\ContentObjectPublicationDescriptionRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @package application.lib.weblcms.browser
 */

/**
 * This is a generic renderer for a set of learning object publications.
 *
 * @package application.weblcms.tool
 * @author  Bart Mollet
 * @author  Tim De Pauw
 */
abstract class ContentObjectPublicationListRenderer
{
    use ClassContext;
    use DependencyInjectionContainerTrait;

    // Types
    public const TOOL_TYPE_ANNOUNCEMENT = 'Announcement';

    public const TYPE_GALLERY = 'GalleryTable';

    public const TYPE_LIST = 'List';

    public const TYPE_SLIDESHOW = 'Slideshow';

    public const TYPE_TABLE = 'Table';

    /**
     * private counter to keep track of first/last status;
     */
    protected $row_counter = 0;

    protected $tool_browser;

    private $actions;

    private $parameters;

    /**
     * Constructor.
     *
     * @param $tool_browser
     * @param $parameters
     */
    public function __construct($tool_browser, $parameters = [])
    {
        $this->initializeContainer();

        $this->parameters = $parameters;
        $this->tool_browser = $tool_browser;
    }

    /**
     * Returns the output of the list renderer as HTML.
     *
     * @return string The HTML.
     */
    abstract public function as_html();

    protected function countContentObjectPublications(): int
    {
        $tool_browser = $this->get_tool_browser();
        $type = $tool_browser->get_publication_type();

        switch ($type)
        {
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME :
                return DataManager::count_my_publications(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $tool_browser->get_user_id()
                );
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL :
                return DataManager::count_content_object_publications(
                    $this->get_publication_conditions()
                );
            default :
                return DataManager::count_content_object_publications_with_view_right_granted_in_category_location(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $tool_browser->get_user_id()
                );
        }
    }

    public static function factory($type, $tool_browser)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
            'ContentObjectPublicationListRenderer';

        if (!class_exists($class))
        {
            throw new Exception(
                Translation::get('ContentObjectPublicationListRendererTypeDoesNotExist', ['type' => $type])
            );
        }

        return new $class($tool_browser);
    }

    /**
     * Formats the given date in a human-readable format.
     *
     * @param $date int A UNIX timestamp.
     *
     * @return string The formatted date.
     */
    public function format_date($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES);

        return DatetimeUtilities::getInstance()->formatLocaleDate($date_format, $date);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    protected function getWorkspaceRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableActions
     */
    public function get_actions()
    {
        return $this->actions;
    }

    public function get_allowed_types(): array
    {
        return $this->tool_browser->get_allowed_types();
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
        $content_object->set_template_registration_id($publication[ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID]);

        return $content_object;
    }

    public function get_course_id()
    {
        return $this->tool_browser->get_course_id();
    }

    /**
     * Returns the value of the given renderer parameter.
     *
     * @param $name string The name of the parameter.
     *
     * @return mixed The value of the parameter.
     */
    public function get_parameter($name)
    {
        return $this->parameters[$name];
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

            if (!$publication[ContentObjectPublication::PROPERTY_EMAIL_SENT] &&
                !$publication[ContentObjectPublication::PROPERTY_HIDDEN])
                // && RightsUtilities::is_allowed(EmailRights::MAIL_ALLOWED, EmailRights::LOCATION, EmailRights ::
                // TYPE))
            {
                $email_url = $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MAIL_PUBLICATION
                    ]
                );

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('SendByEMail'), new FontAwesomeGlyph('envelope'), $email_url,
                        ToolbarItem::DISPLAY_ICON, true
                    )
                );
            }
        }

        $details_url = $this->get_url(
            [
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW
            ]
        );
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Details', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('info-circle'),
                $details_url, ToolbarItem::DISPLAY_ICON
            )
        );

        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DisplayComplex'), new FontAwesomeGlyph('desktop'),
                    $this->get_complex_display_url($publication_id), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();

        $canEditContentObject =
            $this->getWorkspaceRightsService()->canEditContentObject($this->get_user(), $content_object);
        $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublication(
            $this->get_user(), new ContentObjectPublication($publication),
            $this->tool_browser->get_application()->get_course()
        );

        if ($canEditContentObject || $canEditPublicationContentObject)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('EditContentObject', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt'), $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_CONTENT_OBJECT,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );

            if ($content_object instanceof ComplexContentObjectSupport)
            {
                if ($content_object::package() == 'Chamilo\Core\Repository\ContentObject\Assessment')
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('BuildComplexObject', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('cubes'), $this->get_complex_builder_url($publication_id),
                            ToolbarItem::DISPLAY_ICON
                        )
                    );

                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('Preview', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('desktop'), $this->get_complex_display_url($publication_id),
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('BuildPreview', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('cubes'), $this->get_complex_display_url($publication_id),
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }
        }

        if ($has_edit_right)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('EditPublicationDetails', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('edit', [], null, 'fas'), $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );

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
                if (!$first_row)
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('MoveUp', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('sort-up'), $this->get_url(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => $true_up,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('MoveUpNA', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }

                if (!$last_row)
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('MoveDown', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('sort-down'), $this->get_url(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => $true_down,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('MoveDownNA', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('sort-down', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }

            $visibility_url = $this->get_url(
                [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type
                ]
            );

            // New functionality in old code

            if ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
                $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
            {
                $variable = 'PeriodForever';
                $glyph = new FontAwesomeGlyph('clock');
            }
            else
            {
                if (time() < $publication[ContentObjectPublication::PROPERTY_FROM_DATE])
                {
                    $variable = 'PeriodBefore';
                    $glyph = new FontAwesomeGlyph('history', [], null, 'fas');
                }
                elseif (time() > $publication[ContentObjectPublication::PROPERTY_TO_DATE])
                {
                    $variable = 'PeriodAfter';
                    $glyph = new FontAwesomeGlyph('history', ['fa-flip-horizontal'], null, 'fas');
                }
                else
                {
                    $variable = 'PeriodCurrent';
                    $glyph = new FontAwesomeGlyph('clock');
                }
            }

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get($variable, null, StringUtilities::LIBRARIES), $glyph, $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );

            if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
            {
                $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
                $visibilityTranslation = Translation::get('MakeVisible', null, Manager::context());
            }
            else
            {
                $glyph = new FontAwesomeGlyph('eye');
                $visibilityTranslation = Translation::get('MakeInvisible', null, Manager::context());
            }

            $toolbar->add_item(
                new ToolbarItem(
                    $visibilityTranslation, $glyph, $visibility_url, ToolbarItem::DISPLAY_ICON
                )
            );

            // Move the publication
            if ($this->get_tool_browser()->get_parent() instanceof Categorizable &&
                $this->get_tool_browser()->hasCategories())
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveToCategory', null, Manager::context()),
                        new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'), $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        $course = $this->get_tool_browser()->get_course();
        if ($course->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ManageRights', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                    $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true, null, null,
                    Translation::get('ConfirmDeletePublication', null, 'Chamilo\Application\Weblcms')
                )
            );
        }

        if (method_exists($this->get_tool_browser()->get_parent(), 'add_content_object_publication_actions'))
        {
            // $content_object_publication_actions =
            $this->get_tool_browser()->get_parent()->add_content_object_publication_actions($toolbar, $publication);
        }

        return $toolbar;
    }

    public function get_publication_conditions()
    {
        return $this->tool_browser->get_publication_conditions();
    }

    /**
     * @see ContentObjectPublicationBrowser::get_publication_count()
     */
    public function get_publication_count()
    {
        return $this->tool_browser->get_publication_count();
    }

    public function get_publication_type()
    {
        if ($this->tool_browser instanceof BrowserComponent)
        {
            return $this->tool_browser->get_publication_type();
        }
        else
        {
            return 0;
        }
    }

    /**
     * @see ContentObjectPublicationBrowser::get_publications()
     */
    public function get_publications($offset = 0, $max_objects = - 1, OrderProperty $orderBy = null)
    {
        if (!$orderBy)
        {
            $orderBy = $this->tool_browser->getDefaultOrderBy();
        }

        return $this->tool_browser->get_publications($offset, $max_objects, $orderBy);
    }

    public function get_search_condition()
    {
        return $this->tool_browser->get_search_condition();
    }

    public function get_tool_browser()
    {
        return $this->tool_browser;
    }

    public function get_tool_id()
    {
        return $this->tool_browser->get_tool_id();
    }

    /**
     * @see ContentObjectPublicationBrowser::get_url()
     */
    public function get_url($parameters = [], $filter = [], $encode_entities = false)
    {
        return $this->tool_browser->get_url($parameters, $filter, $encode_entities);
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

    /**
     * @see ContentObjectPublicationBrowser::is_allowed()
     */
    public function is_allowed($right, $publication = null)
    {
        return $this->tool_browser->is_allowed($right, $publication);
    }

    /**
     * Checks if a publication is visible for target users
     *
     * @param $publication
     *
     * @return bool
     */
    public function is_visible_for_target_users($publication)
    {
        return (!$publication[ContentObjectPublication::PROPERTY_HIDDEN] &&
            ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
                $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0) ||
            ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] <= time() &&
                $publication[ContentObjectPublication::PROPERTY_TO_DATE] >= time()));
    }

    /**
     * @return string
     */
    public static function package()
    {
        return static::context();
    }

    /**
     * @param $publication
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
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

                usort(
                    $attachments, function ($contentObjectOne, $contentObjectTwo) {
                    return strcasecmp($contentObjectOne->get_title(), $contentObjectTwo->get_title());
                }
                );

                $html[] = '<ul>';

                /**
                 * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $attachments
                 * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject $attachment
                 */
                foreach ($attachments as $attachment)
                {
                    $glyph = $attachment->getGlyph(IdentGlyph::SIZE_MINI);

                    $html[] = '<li><a href="' . $this->tool_browser->get_url(
                            [
                                Manager::PARAM_PUBLICATION => $publication[ContentObjectPublication::PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW_ATTACHMENT,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_OBJECT_ID => $attachment->get_id()
                            ]
                        ) . '">' . $glyph->render() . ' ' . $attachment->get_title() . '</a></li>';
                }
                $html[] = '</ul>';

                return implode(PHP_EOL, $html);
            }
        }

        return '';
    }

    /**
     * Renders the description of the given publication.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_description($publication)
    {
        $content_object_publication_description_renderer = new ContentObjectPublicationDescriptionRenderer(
            $this, $publication
        );

        return $content_object_publication_description_renderer->render();
    }

    /**
     * Renders the icon for the given publication
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The rendered HTML.
     */
    public function render_icon($publication)
    {
        return $this->get_content_object_from_publication($publication)->get_icon_image();
    }

    /**
     * Renders the means to move the given publication to another category.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_move_to_category_action($publication)
    {
        if ($this->get_tool_browser() instanceof Categorizable)
        {

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
                ), new StaticConditionVariable($this->tool_browser->get_parent()->get_course_id())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
                ), new StaticConditionVariable($this->tool_browser->get_parent()->get_tool_id())
            );

            $count = DataManager::count(
                ContentObjectPublicationCategory::class, new DataClassCountParameters(new AndCondition($conditions))
            );

            $count ++;
            if ($count > 1)
            {
                $url = $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                    ], [], true
                );

                $glyph = new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas');

                $link = '<a href="' . $url . '">' . $glyph->render() . '</a>';
            }
            else
            {
                $glyph = new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal', 'text-muted'], null, 'fas');

                $link = $glyph->render();
            }

            return $link;
        }
        else
        {
            return null;
        }
    }

    /**
     * Renders publication actions for the given publication.
     *
     * @param $publication ContentObjectPublication The publication.
     * @param $first       bool True if the publication is the first in the list it is a part of.
     * @param $last        bool True if the publication is the last in the list it is a part of.
     *
     * @return string The rendered HTML.
     */
    public function render_publication_actions($publication, $first, $last)
    {
        $html = [];
        $html[] = $this->get_publication_actions($publication)->as_html();

        return implode($html);
    }

    /**
     * Renders the date when the given publication was published.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_publication_date($publication)
    {
        return $this->format_date($publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE]);
    }

    /**
     * Renders the time period in which the given publication is active.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_publication_period($publication)
    {
        if ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
            $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
        {
            return htmlentities(Translation::get('Forever', null, StringUtilities::LIBRARIES));
        }

        return htmlentities(
            Translation::get(
                'VisibleFromUntil', [
                'FROM' => $this->format_date($publication[ContentObjectPublication::PROPERTY_FROM_DATE]),
                'UNTIL' => $this->format_date($publication[ContentObjectPublication::PROPERTY_TO_DATE])
            ], StringUtilities::LIBRARIES
            )
        );
    }

    /**
     * Renders the users and course_groups the given publication was published for.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_publication_targets($publication)
    {
        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication[ContentObjectPublication::PROPERTY_ID],
                WeblcmsRights::TYPE_PUBLICATION, $this->get_course_id(), WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        return WeblcmsRights::getInstance()->render_target_entities_as_string($target_entities);
    }

    /**
     * Renders information about the repo_viewer of the given publication.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_repository_viewer($publication)
    {
        $user = $this->tool_browser->get_parent()->get_user_info(
            $publication[ContentObjectPublication::PROPERTY_PUBLISHER_ID]
        );

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
     * Renders the title of the given publication.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_title($publication)
    {
        return htmlspecialchars($this->get_content_object_from_publication($publication)->get_title());
    }

    /**
     * Renders the means to toggle visibility for the given publication.
     *
     * @param $publication ContentObjectPublication The publication.
     *
     * @return string The HTML rendering.
     */
    public function render_visibility_action($publication)
    {
        $visibility_url = $this->get_url(
            [
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
            ], [], true
        );
        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
        }

        elseif ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
            $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
        {
            $glyph = new FontAwesomeGlyph('eye');
        }
        else
        {
            $glyph = new FontAwesomeGlyph('clock');
            $visibility_url = 'javascript:void(0)';
        }
        $visibility_link = '<a href="' . $visibility_url . '">' . $glyph->render() . '</a>';

        return $visibility_link;
    }

    protected function retrieveContentObjectPublications(int $offset, int $count, OrderBy $orderBy): ArrayCollection
    {
        $tool_browser = $this->get_tool_browser();

        if ($orderBy->count() == 0)
        {
            $orderBy = $tool_browser->getDefaultOrderBy();
        }

        $type = $tool_browser->get_publication_type();

        switch ($type)
        {
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME :
                return DataManager::retrieve_my_publications(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $orderBy, $offset, $count, $tool_browser->get_user_id()
                );
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL :
                return DataManager::retrieve_content_object_publications(
                    $this->get_publication_conditions(), $orderBy, $offset, $count
                );
            default :
                return DataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $tool_browser->get_location(), $tool_browser->get_entities(), $this->get_publication_conditions(),
                    $orderBy, $offset, $count, $tool_browser->get_user_id()
                );
        }
    }

    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * Sets the value of the given renderer parameter.
     *
     * @param $name  string The name of the parameter.
     * @param $value mixed The new value for the parameter.
     */
    public function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }
}
