<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\ComplexContentObjectPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\PrerequisitesTranslator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerComponent extends TabComponent
{

    private $learning_path_trackers;

    private $learning_path_menu;

    /**
     * The button toolbar
     *
     * @var ButtonToolbar
     */
    protected $buttonToolbar;

    private $navigation;
    const TRACKER_LEARNING_PATH = 'tracker_learning_path';
    const TRACKER_LEARNING_PATH_ITEM = 'tracker_learning_path_item';

    public function build()
    {
        $show_progress = Request :: get(self :: PARAM_SHOW_PROGRESS);
        $learning_path = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $learning_path)
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_error_message(Translation :: get('NoObjectSelected'));
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        // Process some tracking
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH] = $this->get_parent()->retrieve_learning_path_tracker();

        // // Get the currently displayed content object
        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());

        // Update the main tracker
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]->set_progress(
            $this->get_complex_content_object_path()->get_progress());
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]->update();

        $translator = new PrerequisitesTranslator($this->get_current_node());

        if (! $translator->can_execute())
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="error-message">' . Translation :: get('NotYetAllowedToView') . '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $learning_path_item_attempt = $this->get_current_node()->get_current_attempt();

        if (! $learning_path_item_attempt instanceof AbstractItemAttempt)
        {
            $learning_path_item_attempt = $this->get_parent()->create_learning_path_item_tracker(
                $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH],
                $this->get_complex_content_object_item());
            $this->get_current_node()->set_current_attempt($learning_path_item_attempt);
        }
        else
        {
            $learning_path_item_attempt->set_start_time(time());
            $learning_path_item_attempt->update();
        }

        $embedder = Embedder :: factory($this, $this->get_current_node());

        $buttonToolbarRenderer = new ButtonToolBarRenderer($this->getButtonToolbar());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $buttonToolbarRenderer->render();
        $html[] = $embedder->run();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the button toolbar
     */
    protected function getButtonToolbar()
    {
        $translator = Translation :: getInstance();

        if (! isset($this->buttonToolbar))
        {
            $buttonToolbar = new ButtonToolBar();

            $primaryActions = new ButtonGroup();
            $secondaryActions = new ButtonGroup();

            $current_content_object = $this->get_current_node()->get_content_object();

            $this->addCreatorButtons($primaryActions, $translator);
            $this->addUpdateButton($current_content_object, $primaryActions, $translator);
            $this->addDeleteButton($primaryActions, $translator);
            // $this->addMoverButtons($secondaryActions, $translator);
            $this->addMoveButton($secondaryActions, $translator);
            $this->addManageButton($secondaryActions, $translator);

            if ($this->get_action() != self :: ACTION_REPORTING && $this->is_current_step_set())
            {
                $this->addReportingButtons($secondaryActions, $translator);
            }

            $buttonToolbar->addButtonGroup($primaryActions);
            $buttonToolbar->addButtonGroup($secondaryActions);

            $this->buttonToolbar = $buttonToolbar;
        }

        return $this->buttonToolbar;
    }

    /**
     * Builds the attachment url TODO: Currently moved the complex content object item to the selected complex content
     * object item because the wrong parameter was used in the viewer
     *
     * @param $attachment ContentObject
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        $selected_complex_content_object_item_id = $this->get_current_complex_content_object_item()->get_id();

        return parent :: get_content_object_display_attachment_url(
            $attachment,
            $selected_complex_content_object_item_id);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addCreatorButtons($buttonGroup, $translator)
    {
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
            $parameters[self :: PARAM_STEP] = $this->get_current_step();

            $actionSelector = new ActionSelector(
                $this,
                $this->getUser()->getId(),
                $this->get_root_content_object()->get_allowed_types(),
                $parameters,
                array(),
                'btn-primary');

            $actionButton = $actionSelector->getActionButton(
                $translator->getTranslation('CreatorComponent', null, Manager :: context()),
                new BootstrapGlyph('plus'));

            $buttonGroup->addButton($actionButton);
        }
    }

    /**
     *
     * @param $current_content_object
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addUpdateButton($current_content_object, $buttonGroup, $translator)
    {
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()) &&
             RightsService :: getInstance()->canEditContentObject($this->get_user(), $current_content_object))
        {
            $editTitle = $translator->getTranslation('UpdaterComponent', null, Manager :: context());
            $editImage = new FontAwesomeGlyph('pencil');
            $editURL = $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self :: PARAM_STEP => $this->get_current_step()));

            if (! $this->get_current_node()->get_content_object() instanceof LearningPath)
            {
                $editButton = new SplitDropdownButton($editTitle, $editImage, $editURL);

                $editButton->addSubButton(
                    new SubButton(
                        $translator->getTranslation('BuildPrerequisites', null, Manager :: context()),
                        new FontAwesomeGlyph('wrench'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_BUILD_PREREQUISITES,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }
            else
            {
                $editButton = new Button($editTitle, $editImage, $editURL);
            }

            $buttonGroup->addButton($editButton);
        }
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addManageButton($buttonGroup, $translator)
    {
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            if ($this->get_current_content_object() instanceof LearningPath &&
                 count($this->get_current_node()->get_children()) > 1)
            {
                $buttonGroup->addButton(
                    new Button(
                        $translator->getTranslation('ManagerComponent', null, Manager :: context()),
                        new FontAwesomeGlyph('bars'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MANAGE,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }
        }
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addDeleteButton($buttonGroup, $translator)
    {
        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            $buttonGroup->addButton(
                new Button(
                    $translator->getTranslation('DeleterComponent', null, Manager :: context()),
                    new BootstrapGlyph('remove'),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self :: PARAM_STEP => $this->get_current_step()))));
        }
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addMoverButtons($buttonGroup, $translator)
    {
        $moveButton = new DropdownButton(
            $translator->getTranslation('Move', null, Manager :: context()),
            new BootstrapGlyph('random'));

        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            $moveButton->addSubButton(
                new SubButton(
                    $translator->getTranslation('MoverComponent', null, Manager :: context()),
                    new BootstrapGlyph('folder-open'),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_MOVE,
                            self :: PARAM_STEP => $this->get_current_step()))));
        }

        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()) &&
             $this->get_current_node()->has_siblings())
        {
            if (! $this->get_current_node()->is_last_child())
            {
                $moveButton->addSubButton(
                    new SubButton(
                        $translator->getTranslation('MoveDown', null, Utilities :: COMMON_LIBRARIES),
                        new BootstrapGlyph('chevron-down'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_SORT,
                                self :: PARAM_SORT => self :: SORT_DOWN,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }

            if (! $this->get_current_node()->is_first_child())
            {
                $moveButton->addSubButton(
                    new SubButton(
                        $translator->getTranslation('MoveUp', null, Utilities :: COMMON_LIBRARIES),
                        new BootstrapGlyph('chevron-up'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_SORT,
                                self :: PARAM_SORT => self :: SORT_UP,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }
        }

        if ($moveButton->hasButtons())
        {
            $buttonGroup->addButton($moveButton);
        }
    }

    /**
     * Adds a move button where you can directly select a parent / position to which you want to move the selected
     * item
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addMoveButton(ButtonGroup $buttonGroup, Translation $translator)
    {
        if ($this->get_current_node()->is_root() ||
             ! $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            return;
        }

        $moveButton = new DropdownButton(
            $translator->getTranslation('Move', null, Manager :: context()),
            new BootstrapGlyph('random'));

        /** @var ComplexContentObjectPath $path */
        $path = $this->get_complex_content_object_path();
        foreach ($path->get_nodes() as $node)
        {

            $contentObject = $node->get_content_object();

            $margin = 15 * (count($node->get_parents()) - 1);

            if (! $node->is_root())
            {
                $title = '<span style="margin-left: ' . $margin . 'px">' . $translator->getTranslation(
                    'AfterContentObject',
                    array('CONTENT_OBJECT' => $contentObject->get_title()),
                    Manager :: context()) . '</span>';

                $moveButton->addSubButton(
                    new SubButton(
                        $title,
                        '',
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MOVE_DIRECTLY,
                                self :: PARAM_PARENT_ID => $node->get_parent_id(),
                                self :: PARAM_DISPLAY_ORDER => $node->get_complex_content_object_item()->get_display_order() +
                                     1,
                                    self :: PARAM_STEP => $this->get_current_step()))));
            }

            if ($contentObject instanceof LearningPath)
            {
                $margin += 15;

                $title = '<span style="margin-left: ' . $margin . 'px">' . $translator->getTranslation(
                    'FirstItemBelowContentObject',
                    array('CONTENT_OBJECT' => $contentObject->get_title()),
                    Manager :: context()) . '</span>';

                $moveButton->addSubButton(
                    new SubButton(
                        $title,
                        '',
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MOVE_DIRECTLY,
                                self :: PARAM_PARENT_ID => $node->get_id(),
                                self :: PARAM_DISPLAY_ORDER => 1,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }
        }

        $buttonGroup->addButton($moveButton);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addReportingButtons($buttonGroup, $translator)
    {
        $extraButton = new DropdownButton(
            $translator->getTranslation('Extra', null, Manager :: context()),
            new BootstrapGlyph('cog'));

        $extraButton->addSubButton(
            new SubButton(
                $translator->getTranslation('ActivityComponent', null, Manager :: context()),
                new FontAwesomeGlyph('mouse-pointer'),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_ACTIVITY,
                        self :: PARAM_STEP => $this->get_current_step()))));

        $extraButton->addSubButton(
            new SubButton(
                $translator->getTranslation('ReportingComponent', null, Manager :: context()),
                new FontAwesomeGlyph('bar-chart'),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_REPORTING,
                        self :: PARAM_STEP => $this->get_current_step()))));

        $buttonGroup->addButton($extraButton);
    }
}
