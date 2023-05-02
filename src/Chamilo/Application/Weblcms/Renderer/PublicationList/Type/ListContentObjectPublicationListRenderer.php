<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\PagerRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Weblcms\Renderer\PublicationList\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ListContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    /**
     *
     * @var integer
     */
    private $currentPageNumber;

    /**
     *
     * @var integer
     */
    private $numberOfItemsPerPage;

    /**
     *
     * @var integer
     */
    private $numberOfItems;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Pager
     */
    private $pager;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    private $pagerRenderer;

    /**
     * The default number of objects per page
     */
    const DEFAULT_PER_PAGE = 20;
    const PARAM_PER_PAGE = 'per_page';
    const PARAM_PAGE_NUMBER = 'page_nr';

    /**
     * Returns the HTML output of this renderer.
     *
     * @return string The HTML output
     */
    public function as_html()
    {
        $publications = $this->get_page_publications();

        if (!is_array($publications) || count($publications) == 0)
        {
            return Display::normal_message(Translation::get('NoPublications', null, Utilities::COMMON_LIBRARIES), true);
        }

        $html[] = $this->renderHeader();

        foreach ($publications as $index => $publication)
        {
            $first = ($index == 0);
            $last = ($index == count($publications) - 1);
            $html[] = $this->render_publication($publication, $first, $last, $index);
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders a single publication.
     *
     * @param $publication ContentObjectPublication The publication.
     * @param $first boolean True if the publication is the first in the list it is a part of.
     * @param $last boolean True if the publication is the last in the list it is a part of.
     * @return string The rendered HTML.
     */
    public function render_publication($publication, $first = false, $last = false, $position = 0)
    {
        $lastVisitDate = $this->get_tool_browser()->get_last_visit_date();

        $html = array();

        $html[] = '<div class="' . $this->determinePanelClasses() . '">';
        $html[] = '<div class="panel-body">';

        $html[] = $this->renderPublicationHeader($publication);
        $html[] = $this->renderPublicationBody($publication);
        $html[] = $this->renderPublicationFooter($publication);

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function determinePanelClasses()
    {
        $classes = array();

        $classes[] = 'panel';
        $classes[] = 'panel-default';
        $classes[] = 'panel-publication';

        if ($this->hasActions())
        {
            $classes[] = 'panel-publication-with-actions';
        }

        return implode(' ', $classes);
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function renderPublicationBody($publication)
    {
        $html = array();

        $html[] = '<div class="row panel-publication-body">';
        $html[] = '<div class="col-xs-12">';
        $html[] = $this->render_description($publication);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function renderPublicationActions($publication)
    {
        $html = array();

        $html[] = $this->renderPublicationActionsToolbar($publication, false);

        return implode(PHP_EOL, $html);
    }

    public function renderPublicationActionsToolbar($publication, $show_move = true, $ascending = true)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $dropdownButton = new DropdownButton(
            Translation::get('Actions'),
            new FontAwesomeGlyph('cog'),
            Button::DISPLAY_ICON,
            'btn-link');
        $dropdownButton->setDropdownClasses('dropdown-menu-right');

        $publication_id = $publication[ContentObjectPublication::PROPERTY_ID];
        $publication_type = $this->get_publication_type();

        $content_object = $this->get_content_object_from_publication($publication);

        $details_url = $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW));
        $dropdownButton->addSubButton(
            new SubButton(
                Translation::get('ViewDetails', null, Manager::context()),
                Theme::getInstance()->getCommonImagePath('Action/Details'),
                $details_url,
                SubButton::DISPLAY_LABEL));

        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('DisplayComplex'),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_complex_display_url($publication_id),
                    SubButton::DISPLAY_LABEL));
        }

        $has_edit_right = $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication);

        if ($has_edit_right && $publication[ContentObjectPublication::PROPERTY_TOOL] == self::TOOL_TYPE_ANNOUNCEMENT)
        {
            if (! $publication[ContentObjectPublication::PROPERTY_EMAIL_SENT] &&
                 $this->isPublicationVisible($publication))
            {
                $email_url = $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MAIL_PUBLICATION));

                $buttonGroup->addButton(
                    new Button(
                        Translation::get('SendByEMail'),
                        new FontAwesomeGlyph('envelope'),
                        $email_url,
                        Button::DISPLAY_ICON,
                        true,
                        'btn-link'));
            }
        }

        $repositoryRightsService = \Chamilo\Core\Repository\Workspace\Service\RightsService::getInstance();
        $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();

        $canEditContentObject = $repositoryRightsService->canEditContentObject($this->get_user(), $content_object);
        $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublicationObject(
            $this->get_user(),
            new ContentObjectPublication($publication),
            $this->tool_browser->get_application()->get_course());

        if ($canEditContentObject || $canEditPublicationContentObject)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('EditContentObject', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('pencil'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_CONTENT_OBJECT,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)),
                    Button::DISPLAY_ICON,
                    false,
                    'btn-link'));
        }

        if ($has_edit_right)
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('EditPublicationDetails', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getImagePath('Chamilo\Application\Weblcms', 'Action/EditPublication'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)),
                    SubButton::DISPLAY_LABEL));

            if ($content_object instanceof ComplexContentObjectSupport && ($content_object->get_owner_id() ==
                 $this->get_tool_browser()->get_user_id()))
            {
                if (\Chamilo\Core\Repository\Builder\Manager::exists($content_object->package()))
                {
                    $dropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('BuildComplexObject', null, Utilities::COMMON_LIBRARIES),
                            Theme::getInstance()->getCommonImagePath('Action/Build'),
                            $this->get_complex_builder_url($publication_id),
                            SubButton::DISPLAY_LABEL));

                    $dropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('Preview', null, Utilities::COMMON_LIBRARIES),
                            Theme::getInstance()->getCommonImagePath('Action/Preview'),
                            $this->get_complex_display_url($publication_id),
                            SubButton::DISPLAY_LABEL));
                }
                else
                {
                    $dropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('BuildPreview', null, Utilities::COMMON_LIBRARIES),
                            Theme::getInstance()->getCommonImagePath('Action/BuildPreview'),
                            $this->get_complex_display_url($publication_id),
                            SubButton::DISPLAY_LABEL));
                }
            }

            ++ $this->row_counter;
            $first_row = $this->row_counter == 1;
            $last_row = $this->row_counter == $this->get_publication_count();
            $direction = $ascending ? 1 : - 1;
            $true_up = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION_UP * $direction;
            $true_down = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION_DOWN * $direction;

            if ($show_move)
            {
                if (! $first_row)
                {
                    $dropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES),
                            Theme::getInstance()->getCommonImagePath('Action/Up'),
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE,
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => $true_up,
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type)),
                            SubButton::DISPLAY_LABEL));
                }

                if (! $last_row)
                {
                    $dropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES),
                            Theme::getInstance()->getCommonImagePath('Action/Down'),
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE,
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => $true_down,
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type)),
                            SubButton::DISPLAY_LABEL));
                }
            }

            $visibility_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $publication_type));

            // New functionality in old code

            // if ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] == 0 &&
            // $publication[ContentObjectPublication::PROPERTY_TO_DATE] == 0)
            // {
            // $variable = 'PeriodForever';
            // $visibility_image = 'Action/Period';
            // }
            // else
            // {
            // if (time() < $publication[ContentObjectPublication::PROPERTY_FROM_DATE])
            // {
            // $variable = 'PeriodBefore';
            // $visibility_image = 'Action/PeriodBefore';
            // }
            // elseif (time() > $publication[ContentObjectPublication::PROPERTY_TO_DATE])
            // {
            // $variable = 'PeriodAfter';
            // $visibility_image = 'Action/PeriodAfter';
            // }
            // else
            // {
            // $variable = 'PeriodCurrent';
            // $visibility_image = 'Action/Period';
            // }
            // }
            //
            // $dropdownButton->addSubButton(
            // new SubButton(
            // Translation :: get($variable, null, Utilities :: COMMON_LIBRARIES),
            // Theme :: getInstance()->getCommonImagePath($visibility_image),
            // $this->get_url(
            // array(
            // \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager ::
            // ACTION_UPDATE_PUBLICATION,
            // \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)),
            // SubButton :: DISPLAY_LABEL));

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

            $dropdownButton->addSubButton(
                new SubButton(
                    $visibilityTranslation,
                    Theme::getInstance()->getCommonImagePath($visibility_image),
                    $visibility_url,
                    SubButton::DISPLAY_LABEL));

            // Move the publication
            if ($this->get_tool_browser()->get_parent() instanceof Categorizable &&
                 $this->get_tool_browser()->hasCategories())
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('MoveToCategory', null, Manager::context()),
                        Theme::getInstance()->getCommonImagePath('Action/Move'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)),
                        SubButton::DISPLAY_LABEL));
            }
        }

        $course = $this->get_tool_browser()->get_course();

        if ($course->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin())
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('ManageRights', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)),
                    SubButton::DISPLAY_LABEL));
        }

        if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('times'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)),
                    Button::DISPLAY_ICON,
                    Translation::get('ConfirmDeletePublication', null, 'Chamilo\Application\Weblcms'),
                    'btn-link'));
        }

        if (method_exists($this->get_tool_browser()->get_parent(), 'addContentObjectPublicationButtons'))
        {
            $this->get_tool_browser()->get_parent()->addContentObjectPublicationButtons(
                $publication,
                $buttonGroup,
                $dropdownButton);
        }

        $buttonGroup->addButton($dropdownButton);
        $buttonToolBar->addItem($buttonGroup);
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @param string[] $publication
     * @return boolean
     */
    public function hasPublicationBeenModified($publication)
    {
        $publicationCreationDate = $publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE];
        $publicationModificationDate = $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE];

        $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        $publicationModified = $publicationModificationDate > $publicationCreationDate;
        $contentObjectModified = $contentObject->get_modification_date() > $publicationCreationDate;
        $contentModified = ($publicationModified || $contentObjectModified);

        if ($contentModified)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns whether or not the publication is visible
     *
     * @param $publication
     * @return bool
     */
    protected function isPublicationVisible($publication)
    {
        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            return false;
        }

        $fromDate = $publication[ContentObjectPublication::PROPERTY_FROM_DATE];
        $toDate = $publication[ContentObjectPublication::PROPERTY_TO_DATE];

        if ($fromDate == 0 && $toDate == 0)
        {
            return true;
        }

        return $fromDate <= time() && $toDate >= time();
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function renderVisiblePublicationDate($publication)
    {
        $publicationCreationDate = $publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE];
        $publicationModificationDate = $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE];

        $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        $publicationModified = $publicationModificationDate > $publicationCreationDate;
        $contentObjectModified = $contentObject->get_modification_date() > $publicationCreationDate;
        $contentModified = ($publicationModified || $contentObjectModified);

        if ($contentModified)
        {
            if ($contentObjectModified && $publicationModified)
            {
                if ($contentObject->get_modification_date() > $publicationModificationDate)
                {
                    $visibleDate = $this->format_date($contentObject->get_modification_date());
                }
                else
                {
                    $visibleDate = $this->format_date($publicationModificationDate);
                }
            }
            elseif ($contentObjectModified)
            {
                $visibleDate = $this->format_date($contentObject->get_modification_date());
            }
            else
            {
                $visibleDate = $this->format_date($publicationModificationDate);
            }
        }
        else
        {
            $visibleDate = $this->format_date($publicationCreationDate);
        }

        $html = array();

        if ($contentModified)
        {
            $html[] = '<span class="text-danger">';
        }

        $html[] = '<span class="glyphicon glyphicon-time"></span>';
        $html[] = $visibleDate;

        if ($contentModified)
        {
            $html[] = '</span>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function getTitleUrl($publication)
    {
        $titleParameters = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]);

        if ($this->get_content_object_from_publication($publication) instanceof ComplexContentObjectSupport)
        {
            $titleParameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;
        }
        else
        {
            $titleParameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        }

        return $this->get_url($titleParameters);
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function renderPublicationHeader($publication)
    {
        $html = array();

        $html[] = '<div class="row panel-publication-header">';

        $html[] = '<div class="col-xs-12 col-sm-10 panel-publication-header-title">';
        $html[] = '<h3>';
        $html[] = '<a class="title" href="' . $this->getTitleUrl($publication) . '">' . $this->render_title(
            $publication) . '</a>';
        $html[] = '<span class="labels">' . $this->renderPublicationLabels($publication) . '</span>';
        $html[] = '</h3>';
        $html[] = '<small>' . $this->render_repository_viewer($publication) . '</small>';

        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-sm-2 panel-publication-header-actions">';

        $html[] = $this->renderPublicationActions($publication);

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function renderPublicationLabels($publication)
    {
        $lastVisitDate = $this->get_tool_browser()->get_last_visit_date();

        $html = array();

        if ($this->hasActions())
        {
            $checkboxHtml = array();

            $checkboxHtml[] = '<span class="label-checkbox checkbox checkbox-primary">';
            $checkboxHtml[] = '<input type="checkbox" class="publication-select styled styled-primary" name="' .
                 Manager::PARAM_PUBLICATION . '[]" value="' . $publication[ContentObjectPublication::PROPERTY_ID] . '"/>';
            $checkboxHtml[] = '<label></label>';
            $checkboxHtml[] = '</span>';

            $html[] = implode('', $checkboxHtml);
        }

        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            $html[] = '<span class="label label-warning">' . Translation::get('PublicationLabelHidden') . '</span>';
        }
        else
        {
            $hasLimitedPeriod = $publication[ContentObjectPublication::PROPERTY_FROM_DATE] != 0 &&
                 $publication[ContentObjectPublication::PROPERTY_TO_DATE] != 0;

            if ($hasLimitedPeriod)
            {
                if (time() < $publication[ContentObjectPublication::PROPERTY_FROM_DATE])
                {
                    $html[] = '<span class="label label-warning">' . Translation::get('PublicationLabelNotYetVisible') .
                         '</span>';
                }
                elseif (time() > $publication[ContentObjectPublication::PROPERTY_TO_DATE])
                {
                    $html[] = '<span class="label label-warning">' . Translation::get('PublicationLabelNoLongerVisible') .
                         '</span>';
                }
            }
        }

        if ($publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE] >= $lastVisitDate)
        {
            $html[] = '<span class="label label-primary">' . Translation::get('PublicationLabelNew') . '</span>';
        }

        if ($this->hasPublicationBeenModified($publication))
        {
            $html[] = '<span class="label label-danger">' . Translation::get('PublicationLabelEdited') . '</span>';
        }

        if ($publication[ContentObjectPublication::PROPERTY_EMAIL_SENT])
        {
            $html[] = '<span class="label label-success">' . Translation::get('PublicationLabelEmailSent') . '</span>';
        }

        return implode('', $html);
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function renderPublicationFooter($publication)
    {
        $html = array();

        $html[] = '<div class="row panel-publication-footer">';

        $html[] = '<div class="col-xs-12 col-sm-3 panel-publication-footer-date">';
        $html[] = $this->renderVisiblePublicationDate($publication);
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-sm-6 panel-publication-footer-visibility">';
        $html[] = $this->renderVisibilityData($publication);
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-sm-3 panel-publication-footer-targets">';
        $html[] = '<span class="glyphicon glyphicon-user"></span>';
        $html[] = $this->render_publication_targets($publication);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $publication
     * @return string
     */
    public function renderVisibilityData($publication)
    {
        $html = array();
        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            $html[] = '<span class="text-warning">';
            $html[] = '<span class="glyphicon glyphicon-eye-close"></span>';
            $html[] = Translation::get('PublicationLabelHidden');
            $html[] = '</span>';
        }
        elseif ($publication[ContentObjectPublication::PROPERTY_FROM_DATE] != 0 ||
             $publication[ContentObjectPublication::PROPERTY_TO_DATE] != 0)
        {
            $html[] = '<span class="glyphicon glyphicon-eye-open"></span>';
            $html[] = $this->render_publication_period($publication);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Pager
     */
    public function getPager()
    {
        if (is_null($this->pager))
        {
            $this->pager = new Pager(
                $this->getNumberOfItemsPerPage() == 'all' ? $this->getNumberOfItems() : $this->getNumberOfItemsPerPage(),
                1,
                $this->getNumberOfItems(),
                $this->getCurrentPageNumber());
        }

        return $this->pager;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    public function getPagerRenderer()
    {
        if (is_null($this->pagerRenderer))
        {
            $this->pagerRenderer = new PagerRenderer($this->getPager());
        }

        return $this->pagerRenderer;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItemsPerPage()
    {
        if (is_null($this->numberOfItemsPerPage))
        {
            $this->numberOfItemsPerPage = $this->get_tool_browser()->getRequest()->query->get(
                self::PARAM_PER_PAGE,
                self::DEFAULT_PER_PAGE);
        }

        return $this->numberOfItemsPerPage;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItems()
    {
        if (is_null($this->numberOfItems))
        {
            $this->numberOfItems = $this->get_publication_count();
        }

        return $this->numberOfItems;
    }

    /**
     *
     * @return integer
     */
    public function getCurrentPageNumber()
    {
        if (is_null($this->currentPageNumber))
        {
            $this->currentPageNumber = $this->get_tool_browser()->getRequest()->query->get(self::PARAM_PAGE_NUMBER, 1);
        }

        return $this->currentPageNumber;
    }

    /**
     *
     * @return string
     */
    public function renderNavigation()
    {
        $pager = $this->getPager();
        $pagerRenderer = $this->getPagerRenderer();

        $queryParameters = $this->get_tool_browser()->get_parameters();
        $queryParameters[self::PARAM_PAGE_NUMBER] = $this->getCurrentPageNumber();

        return $pagerRenderer->renderPaginationWithPageLimit($queryParameters, self::PARAM_PAGE_NUMBER);
    }

    public function get_page_publications()
    {
        $pager = $this->getPager();
        return $this->get_publications($pager->getCurrentRangeOffset(), $this->getNumberOfItemsPerPage());
    }

    /**
     *
     * @return string
     */
    public function renderHeader()
    {
        $html = array();

        if ($this->hasActions())
        {
            $tableFormActions = $this->get_actions()->get_form_actions();
            $firstFormAction = array_shift($tableFormActions);

            $html[] = '<form class="form-list-view" method="post" action="' . $firstFormAction->get_action() .
                 '" name="form_' . $this->getListName() . '">';
        }

        $html[] = '<div class="row">';

        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($this->hasActions())
        {
            $html[] = $this->renderActions();
        }

        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-search">';
        $html[] = $this->renderItemsPerPageSelector();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderFooter()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($this->hasActions())
        {
            $html[] = $this->renderActions();
        }

        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-pagination">';
        $html[] = $this->renderNavigation();
        $html[] = '</div>';

        $html[] = '</div>';

        if ($this->hasActions())
        {
            $html[] = '<input type="submit" name="Submit" value="Submit" class="hidden" />';
            $html[] = '</form>';
            $html[] = ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'list.view.selector.js');
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderItemsPerPageSelector()
    {
        $sourceDataCount = $this->getNumberOfItems();

        // if ($sourceDataCount <= $this->getNumberOfItemsPerPage())
        // {
        // return '';
        // }

        $queryParameters = $this->get_tool_browser()->get_parameters();
        $queryParameters[self::PARAM_PAGE_NUMBER] = $this->getCurrentPageNumber();

        $translationVariables = array();
        $translationVariables[Application::PARAM_CONTEXT] = Manager::package();
        $translationVariables[PagerRenderer::PAGE_SELECTOR_TRANSLATION_TITLE] = 'ShowNumberOfPublicationsPerPage';
        $translationVariables[PagerRenderer::PAGE_SELECTOR_TRANSLATION_ROW] = 'NumberOfPublicationsPerPage';

        return $this->getPagerRenderer()->renderItemsPerPageSelector(
            $queryParameters,
            self::PARAM_PER_PAGE,
            $translationVariables);
    }

    /**
     *
     * @return boolean
     */
    protected function hasActions()
    {
        $hasActions = $this->get_actions() instanceof TableFormActions && $this->get_actions()->has_form_actions();
        $hasPublications = $this->getNumberOfItems() > 0;
        $hasRights = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        return $hasActions && $hasPublications && $hasRights;
    }

    /**
     *
     * @return string
     */
    public function renderActions()
    {
        $formActions = $this->get_actions()->get_form_actions();
        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                Translation::get('SelectAll', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('square-o'),
                '#',
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'btn-sm select-all'));

        $buttonToolBar->addItem(
            new Button(
                Translation::get('UnselectAll', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('check-square-o'),
                '#',
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'btn-sm select-none'));

        $actionButtonGroup = new ButtonGroup();

        $button = new SplitDropdownButton(
            $firstAction->get_title(),
            null,
            $firstAction->get_action(),
            Button::DISPLAY_LABEL,
            $firstAction->getConfirmation(),
            'btn-sm btn-table-action');
        $button->setDropdownClasses('btn-table-action');

        foreach ($formActions as $formAction)
        {
            $button->addSubButton(
                new SubButton(
                    $formAction->get_title(),
                    null,
                    $formAction->get_action(),
                    Button::DISPLAY_LABEL,
                    $formAction->getConfirmation()));
        }

        $actionButtonGroup->addButton($button);

        $buttonToolBar->addButtonGroup($actionButtonGroup);

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $html = array();

        $html[] = $buttonToolBarRenderer->render();
        $html[] = '<input type="hidden" name="' . $this->getListName() . '_namespace" value="' .
             $this->get_actions()->get_namespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $this->getListName() . '"/>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getListName()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(__CLASS__, true);
    }
}
