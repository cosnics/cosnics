<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form\DirectMoverForm;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\PrerequisitesTranslator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

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
        $show_progress = Request::get(self::PARAM_SHOW_PROGRESS);
        $learning_path = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail::getInstance();

        if (!$learning_path)
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_error_message(Translation::get('NoObjectSelected'));
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        // Process some tracking
        $this->learning_path_trackers[self::TRACKER_LEARNING_PATH] =
            $this->get_parent()->retrieve_learning_path_tracker();

        // // Get the currently displayed content object
        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());

        $translator = new PrerequisitesTranslator($this->get_current_node());

        if (!$this->canEditComplexContentObjectPathNode($this->get_current_node()) && !$translator->can_execute())
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="error-message">' . Translation::get('NotYetAllowedToView') . '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $learning_path_item_attempt = $this->get_current_node()->get_current_attempt();

        if (!$learning_path_item_attempt instanceof AbstractItemAttempt)
        {
            $learning_path_item_attempt = $this->get_parent()->create_learning_path_item_tracker(
                $this->learning_path_trackers[self::TRACKER_LEARNING_PATH],
                $this->get_complex_content_object_item()
            );
            $this->get_current_node()->set_current_attempt($learning_path_item_attempt);
        }
        else
        {
            $learning_path_item_attempt->set_start_time(time());
            $learning_path_item_attempt->update();
        }

        $embedder = Embedder::factory($this, $this->get_current_node());

        $buttonToolbarRenderer = new ButtonToolBarRenderer($this->getButtonToolbar());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $buttonToolbarRenderer->render();
        $html[] = $this->renderMovePanel();
        $html[] = $embedder->run();

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'KeyboardNavigation.js'
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function recalculateLearningPathProgress()
    {
        $this->learning_path_trackers[self::TRACKER_LEARNING_PATH]->set_progress(
            $this->get_complex_content_object_path()->get_progress()
        );
        
        $this->learning_path_trackers[self::TRACKER_LEARNING_PATH]->update();
    }

    /**
     * Returns the button toolbar
     */
    protected function getButtonToolbar()
    {
        $translator = Translation::getInstance();

        if (!isset($this->buttonToolbar))
        {
            $buttonToolbar = new ButtonToolBar();

            $primaryActions = new ButtonGroup();
            $secondaryActions = new ButtonGroup();

            $current_content_object = $this->get_current_node()->get_content_object();

            $this->addCreatorButtons($primaryActions, $translator);
            $this->addNodeSpecificButtons($primaryActions, $secondaryActions);

            $this->addUpdateButton($current_content_object, $primaryActions, $translator);
            $this->addDeleteButton($primaryActions, $translator, $current_content_object);
            // $this->addMoverButtons($secondaryActions, $translator);
            $this->addMoveButton($secondaryActions, $translator);
            $this->addManageButton($secondaryActions, $translator, $current_content_object);

            if ($this->get_action() != self::ACTION_REPORTING)
            {
                $this->addReportingButtons($secondaryActions, $translator, $current_content_object);
            }

            $buttonToolbar->addButtonGroup($primaryActions);
            $buttonToolbar->addButtonGroup($secondaryActions);

            $this->buttonToolbar = $buttonToolbar;
        }

        return $this->buttonToolbar;
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     */
    public function addNodeSpecificButtons(ButtonGroup $primaryActions, ButtonGroup $secondaryActions)
    {
        $object_namespace = $this->get_current_node()->get_content_object()->package();
        $integration_class_name = $object_namespace . '\Integration\\' . self::package() . '\Manager';

        if (class_exists($integration_class_name))
        {
            try
            {
                $factory = new ApplicationFactory(
                    $integration_class_name::context(),
                    new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
                );
                $component = $factory->getComponent(null, false);
                $component->get_node_tabs($primaryActions, $secondaryActions, $this->get_current_node());
            }
            catch (\Exception $exception)
            {
            }
        }
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

        return parent::get_content_object_display_attachment_url($attachment, $selected_complex_content_object_item_id);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addCreatorButtons($buttonGroup, $translator)
    {
        if ($this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;

            if ($this->get_current_content_object() instanceof LearningPath)
            {
                $parameters[self::PARAM_STEP] = $this->get_current_step();
                $parameters[self::PARAM_CONTENT_OBJECT_ID] = $this->get_current_content_object()->getId();
            }
            else
            {
                $parameters[self::PARAM_STEP] = $this->get_current_node()->get_parent_id();
                $parameters[self::PARAM_CONTENT_OBJECT_ID] =
                    $this->get_current_node()->get_parent()->get_content_object()->getId();
            }

            $actionSelector = new ActionSelector(
                $this,
                $this->getUser()->getId(),
                $this->get_root_content_object()->get_allowed_types(),
                $parameters,
                array(),
                'btn-primary'
            );

            $actionButton = $actionSelector->getActionButton(
                $translator->getTranslation('CreatorComponent', null, Manager::context()),
                new BootstrapGlyph('plus')
            );

            $buttonGroup->addButton($actionButton);
        }
    }

    /**
     *
     * @param ContentObject $current_content_object
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addUpdateButton($current_content_object, $buttonGroup, $translator)
    {
        if ($this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            $editTitle = $translator->getTranslation('UpdaterComponent', null, Manager::context());
            $editImage = new FontAwesomeGlyph('pencil');
            $editURL = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_STEP => $this->get_current_step(),
                    self::PARAM_CONTENT_OBJECT_ID => $current_content_object->getId()
                )
            );

            if (!$this->get_current_node()->get_content_object() instanceof LearningPath)
            {
                $editButton = new SplitDropdownButton($editTitle, $editImage, $editURL);

                $editButton->addSubButton(
                    new SubButton(
                        $translator->getTranslation('BuildPrerequisites', null, Manager::context()),
                        new FontAwesomeGlyph('wrench'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_BUILD_PREREQUISITES,
                                self::PARAM_STEP => $this->get_current_step(),
                                self::PARAM_CONTENT_OBJECT_ID => $current_content_object->getId()
                            )
                        )
                    )
                );
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
     * @param $currentContentObject
     */
    protected function addManageButton($buttonGroup, $translator, $currentContentObject)
    {
        if ($this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            if ($this->get_current_content_object() instanceof LearningPath &&
                count($this->get_current_node()->get_children()) > 1
            )
            {
                $buttonGroup->addButton(
                    new Button(
                        $translator->getTranslation('ManagerComponent', null, Manager::context()),
                        new FontAwesomeGlyph('bars'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_MANAGE,
                                self::PARAM_STEP => $this->get_current_step(),
                                self::PARAM_CONTENT_OBJECT_ID => $currentContentObject->getId()
                            )
                        )
                    )
                );
            }
        }
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     * @param $currentContentObject
     */
    protected function addDeleteButton($buttonGroup, $translator, $currentContentObject)
    {
        if (!$this->get_current_node()->is_root() &&
            $this->canEditComplexContentObjectPathNode($this->get_current_node()->get_parent())
        )
        {
            $buttonGroup->addButton(
                new Button(
                    $translator->getTranslation('DeleterComponent', null, Manager::context()),
                    new BootstrapGlyph('remove'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self::PARAM_STEP => $this->get_current_step(),
                            self::PARAM_CONTENT_OBJECT_ID => $currentContentObject->getId()
                        )
                    )
                )
            );
        }
    }

    // /**
    // *
    // * @param ButtonGroup $buttonGroup
    // * @param Translation $translator
    // */
    // protected function addMoverButtons($buttonGroup, $translator)
    // {
    // $moveButton = new DropdownButton(
    // $translator->getTranslation('Move', null, Manager :: context()),
    // new BootstrapGlyph('random'));
    //
    // if (! $this->get_current_node()->is_root() &&
    // $this->canEditComplexContentObjectPathNode($this->get_current_node()->get_parent()))
    // {
    // $moveButton->addSubButton(
    // new SubButton(
    // $translator->getTranslation('MoverComponent', null, Manager :: context()),
    // new BootstrapGlyph('folder-open'),
    // $this->get_url(
    // array(
    // self :: PARAM_ACTION => self :: ACTION_MOVE,
    // self :: PARAM_STEP => $this->get_current_step()))));
    // }
    //
    // if (! $this->get_current_node()->is_root() &&
    // $this->canEditComplexContentObjectPathNode($this->get_current_node()->get_parent()) &&
    // $this->get_current_node()->has_siblings())
    // {
    // if (! $this->get_current_node()->is_last_child())
    // {
    // $moveButton->addSubButton(
    // new SubButton(
    // $translator->getTranslation('MoveDown', null, Utilities :: COMMON_LIBRARIES),
    // new BootstrapGlyph('chevron-down'),
    // $this->get_url(
    // array(
    // self :: PARAM_ACTION => self :: ACTION_SORT,
    // self :: PARAM_SORT => self :: SORT_DOWN,
    // self :: PARAM_STEP => $this->get_current_step()))));
    // }
    //
    // if (! $this->get_current_node()->is_first_child())
    // {
    // $moveButton->addSubButton(
    // new SubButton(
    // $translator->getTranslation('MoveUp', null, Utilities :: COMMON_LIBRARIES),
    // new BootstrapGlyph('chevron-up'),
    // $this->get_url(
    // array(
    // self :: PARAM_ACTION => self :: ACTION_SORT,
    // self :: PARAM_SORT => self :: SORT_UP,
    // self :: PARAM_STEP => $this->get_current_step()))));
    // }
    // }
    //
    // if ($moveButton->hasButtons())
    // {
    // $buttonGroup->addButton($moveButton);
    // }
    // }

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
            !$this->canEditComplexContentObjectPathNode($this->get_current_node()->get_parent())
        )
        {
            return;
        }
        //
        // $moveButton = new DropdownButton(
        // $translator->getTranslation('Move', null, Manager:: context()),
        // new BootstrapGlyph('random')
        // );
        //
        // $descendants = $this->get_current_node()->get_descendants();
        //
        // /** @var ComplexContentObjectPath $path */
        // $path = $this->get_complex_content_object_path();
        // foreach ($path->get_nodes() as $node)
        // {
        //
        // if ($node == $this->get_current_node() || in_array($node, $descendants))
        // {
        // continue;
        // }
        //
        // $contentObject = $node->get_content_object();
        //
        // $margin = 15 * (count($node->get_parents()) - 1);
        //
        // if (!$node->is_root())
        // {
        // $title = '<span style="margin-left: ' . $margin . 'px">' . $translator->getTranslation(
        // 'AfterContentObject',
        // array('CONTENT_OBJECT' => $contentObject->get_title()),
        // Manager:: context()
        // ) . '</span>';
        //
        // $moveButton->addSubButton(
        // new SubButton(
        // $title,
        // '',
        // $this->get_url(
        // array(
        // self :: PARAM_ACTION => self :: ACTION_MOVE_DIRECTLY,
        // self :: PARAM_PARENT_ID => $node->get_parent_id(),
        // self :: PARAM_DISPLAY_ORDER => $node->get_complex_content_object_item()
        // ->get_display_order() +
        // 1,
        // self :: PARAM_STEP => $this->get_current_step()
        // )
        // )
        // )
        // );
        // }
        //
        // if ($contentObject instanceof LearningPath)
        // {
        // $margin += 15;
        //
        // $title = '<span style="margin-left: ' . $margin . 'px">' . $translator->getTranslation(
        // 'FirstItemBelowContentObject',
        // array('CONTENT_OBJECT' => $contentObject->get_title()),
        // Manager:: context()
        // ) . '</span>';
        //
        // $moveButton->addSubButton(
        // new SubButton(
        // $title,
        // '',
        // $this->get_url(
        // array(
        // self :: PARAM_ACTION => self :: ACTION_MOVE_DIRECTLY,
        // self :: PARAM_PARENT_ID => $node->get_id(),
        // self :: PARAM_DISPLAY_ORDER => 1,
        // self :: PARAM_STEP => $this->get_current_step()
        // )
        // )
        // )
        // );
        // }
        // }

        $moveButton = new Button(
            $translator->getTranslation('Move', null, Manager::context()),
            new BootstrapGlyph('random'),
            '#',
            Button::DISPLAY_ICON_AND_LABEL,
            false,
            'mover-open'
        );

        $buttonGroup->addButton($moveButton);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addReportingButtons($buttonGroup, $translator, $currentContentObject)
    {
        $extraButton = new DropdownButton(
            $translator->getTranslation('Extra', null, Manager::context()),
            new BootstrapGlyph('cog')
        );

        $extraButton->addSubButton(
            new SubButton(
                $translator->getTranslation('ActivityComponent', null, Manager::context()),
                new FontAwesomeGlyph('mouse-pointer'),
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_ACTIVITY,
                        self::PARAM_STEP => $this->get_current_step(),
                        self::PARAM_CONTENT_OBJECT_ID => $currentContentObject->getId()
                    )
                )
            )
        );

        $extraButton->addSubButton(
            new SubButton(
                $translator->getTranslation('ReportingComponent', null, Manager::context()),
                new FontAwesomeGlyph('bar-chart'),
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_REPORTING,
                        self::PARAM_STEP => $this->get_current_step(),
                        self::PARAM_CONTENT_OBJECT_ID => $currentContentObject->getId()
                    )
                )
            )
        );

        $buttonGroup->addButton($extraButton);
    }

    /**
     * Renders the move panel that will be made visible when the move button is pressed
     *
     * @return string
     */
    protected function renderMovePanel()
    {
        $translator = Translation::getInstance();

        $form = new DirectMoverForm(
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_MOVE_DIRECTLY,
                    self::PARAM_CONTENT_OBJECT_ID => $this->get_current_content_object()->getId()
                )
            ),
            $this->get_complex_content_object_path(),
            $this->get_current_node()
        );

        $html = array();

        $html[] = '<div class="panel panel-default" id="mover" style="display: none;">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = '<div class="pull-right">';
        $html[] = '<a href="#" id="mover-close" style="color: black; opacity: 0.5">';
        $html[] = '<span class="inline-glyph glyphicon glyphicon-remove"></span>';
        $html[] = '</a>';
        $html[] = '</div>';
        $html[] = $translator->getTranslation('Move', null, Manager::context());
        $html[] = '</h3>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $form->toHtml();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
