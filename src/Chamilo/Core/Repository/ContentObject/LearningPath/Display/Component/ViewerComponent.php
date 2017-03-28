<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form\DirectMoverForm;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\PrerequisitesTranslator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
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

    /**
     * The button toolbar
     *
     * @var ButtonToolbar
     */
    protected $buttonToolbar;

    public function build()
    {
        $show_progress = Request::get(self::PARAM_SHOW_PROGRESS);
        $learning_path = $this->get_root_content_object();

        $trail = BreadcrumbTrail::getInstance();

        if (!$learning_path)
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_error_message(Translation::get('NoObjectSelected'));
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $learningPathTrackingService = $this->getLearningPathTrackingService();

        $learningPathTrackingService->trackAttemptForUser(
            $this->get_root_content_object(), $this->getCurrentLearningPathTreeNode(), $this->getUser()
        );

        if (!$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="error-message">' . Translation::get('NotYetAllowedToView') . '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $embedder = Embedder::factory(
            $this, $this->getLearningPathTrackingService(), $this->get_root_content_object(),
            $this->getCurrentLearningPathTreeNode()
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($this->getButtonToolbar());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $buttonToolbarRenderer->render();
        $html[] = $this->renderMovePanel();

        if ($this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()) &&
            $this->getCurrentLearningPathTreeNode()->getLearningPathChild() &&
            $this->getCurrentLearningPathTreeNode()->getLearningPathChild()->isBlocked()
        )
        {
            $html[] = '<div class="alert alert-warning">' .
                Translation::getInstance()->getTranslation('ThisStepIsRequired') . '</div>';
        }

        $html[] = $embedder->run();

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'KeyboardNavigation.js'
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
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

            $current_content_object = $this->getCurrentLearningPathTreeNode()->getContentObject();

            $this->addCreatorButtons($primaryActions, $translator);
            $this->addNodeSpecificButtons($primaryActions, $secondaryActions);

            $this->addUpdateButton($current_content_object, $primaryActions, $translator);
            $this->addDeleteButton($primaryActions, $translator, $current_content_object);
            $this->addMoveButton($secondaryActions, $translator);
            $this->addBlockedStatusButton($secondaryActions, $translator, $this->getCurrentLearningPathTreeNode());
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
        $object_namespace = $this->getCurrentLearningPathTreeNode()->getContentObject()->package();
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
                $component->get_node_tabs($primaryActions, $secondaryActions, $this->getCurrentLearningPathTreeNode());
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
//        $selected_complex_content_object_item_id = $this->get_current_complex_content_object_item()->get_id();

        return parent::get_content_object_display_attachment_url($attachment, $selected_complex_content_object_item_id);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addCreatorButtons($buttonGroup, $translator)
    {
        if ($this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
            $parameters[self::PARAM_CHILD_ID] = $this->getCurrentLearningPathTreeNode()->getId();

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

            $parameters[CreatorComponent::PARAM_CREATE_MODE] = CreatorComponent::CREATE_MODE_FOLDER;

            $folderSelector = new ActionSelector(
                $this, $this->getUser()->getId(), array(LearningPath::class_name()), $parameters
            );

            $folderButton = $folderSelector->getActionButton(
                $translator->getTranslation('CreateFolder', null, Manager::context()),
                new BootstrapGlyph('plus')
            );

            $buttonGroup->addButton($folderButton);
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
        if ($this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            $editTitle = $translator->getTranslation('UpdaterComponent', null, Manager::context());
            $editImage = new FontAwesomeGlyph('pencil');
            $editURL = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                )
            );

            $editButton = new Button($editTitle, $editImage, $editURL);

            $buttonGroup->addButton($editButton);
        }
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     * @param ContentObject $currentContentObject
     */
    protected function addManageButton($buttonGroup, $translator, $currentContentObject)
    {
        if ($this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            if ($this->getCurrentLearningPathTreeNode()->hasChildNodes())
            {
                $buttonGroup->addButton(
                    new Button(
                        $translator->getTranslation('ManagerComponent', null, Manager::context()),
                        new FontAwesomeGlyph('bars'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_MANAGE,
                                self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
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
     * @param ContentObject $currentContentObject
     */
    protected function addDeleteButton($buttonGroup, $translator, $currentContentObject)
    {
        if (!$this->getCurrentLearningPathTreeNode()->isRootNode() &&
            $this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()->getParentNode())
        )
        {
            $buttonGroup->addButton(
                new Button(
                    $translator->getTranslation('DeleterComponent', null, Manager::context()),
                    new BootstrapGlyph('remove'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                        )
                    )
                )
            );
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
        if ($this->getCurrentLearningPathTreeNode()->isRootNode() ||
            !$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()->getParentNode())
        )
        {
            return;
        }

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
     * Adds a button to block / unblock the status of the given learning path tree node
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    protected function addBlockedStatusButton(
        ButtonGroup $buttonGroup, Translation $translator, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $translationVariable = ($learningPathTreeNode->getLearningPathChild() &&
            $learningPathTreeNode->getLearningPathChild()->isBlocked()) ?
            'MarkAsOptional' : 'MarkAsRequired';

        $icon = ($learningPathTreeNode->getLearningPathChild() &&
            $learningPathTreeNode->getLearningPathChild()->isBlocked()) ?
            'unlock' : 'ban';

        $moveButton = new Button(
            $translator->getTranslation($translationVariable, null, Manager::context()),
            new FontAwesomeGlyph($icon),
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_TOGGLE_BLOCKED_STATUS,
                    self::PARAM_CHILD_ID => $learningPathTreeNode->getId()
                )
            ),
            Button::DISPLAY_ICON_AND_LABEL
        );

        $buttonGroup->addButton($moveButton);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     * @param ContentObject $currentContentObject
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
                        self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
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
                        self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
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
                    self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId(),
                    self::PARAM_CONTENT_OBJECT_ID => $this->getCurrentContentObject()->getId()
                )
            ),
            $this->getLearningPathTree(),
            $this->getCurrentLearningPathTreeNode()
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
