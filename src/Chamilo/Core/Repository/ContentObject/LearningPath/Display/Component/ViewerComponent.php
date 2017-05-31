<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form\DirectMoverForm;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\LearningPathActionSelector;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ViewerComponent extends BaseHtmlTreeComponent
{

    /**
     * The button toolbar
     *
     * @var ButtonToolbar
     */
    protected $buttonToolbar;

    public function build()
    {
        $translator = Translation::getInstance();

        $learning_path = $this->get_root_content_object();

        if (!$learning_path)
        {
            throw new ObjectNotExistException($translator->getTranslation('LearningPath'));
        }

        $learningPathTrackingService = $this->getLearningPathTrackingService();

        $learningPathTrackingService->trackAttemptForUser(
            $this->get_root_content_object(), $this->getCurrentLearningPathTreeNode(), $this->getUser()
        );

        if (!$this->canEditCurrentLearningPathTreeNode() &&
            $learningPathTrackingService->isCurrentLearningPathTreeNodeBlocked(
                $learning_path, $this->getUser(), $this->getCurrentLearningPathTreeNode()
            )
        )
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="alert alert-danger">';
            $html[] = Translation::get('NotYetAllowedToView');

            $responsibleNodes = $learningPathTrackingService->getResponsibleNodesForBlockedLearningPathTreeNode(
                $learning_path, $this->getUser(), $this->getCurrentLearningPathTreeNode()
            );

            $html[] = '<br /><br />';
            $html[] = '<ul>';

            $automaticNumberingService = $this->getAutomaticNumberingService();

            foreach ($responsibleNodes as $responsibleNode)
            {
                $nodeUrl = $this->get_url(array(self::PARAM_CHILD_ID => $responsibleNode->getId()));

                $html[] = '<li>';
                $html[] = '<a href="' . $nodeUrl . '">';

                $html[] = $automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode(
                    $responsibleNode
                );

                $html[] = '</a>';
                $html[] = '</li>';
            }

            $html[] = '</ol>';
            $html[] = '</div>';
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

        if($this->canEditCurrentLearningPathTreeNode())
        {
            $html[] = $this->renderMovePanel();
        }

        if ($this->canEditCurrentLearningPathTreeNode() &&
            (
                (
                    $this->getCurrentLearningPathTreeNode()->getLearningPathChild() &&
                    $this->getCurrentLearningPathTreeNode()->getLearningPathChild()->isBlocked()
                ) ||
                $this->get_root_content_object()->enforcesDefaultTraversingOrder()
            )
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
            $this->buttonToolbar = $buttonToolbar;

            if (!$this->canEditCurrentLearningPathTreeNode())
            {
                return $this->buttonToolbar;
            }

            $primaryActions = new ButtonGroup();
            $secondaryActions = new ButtonGroup();
            $tertiaryActions = new ButtonGroup();

            $this->addCreatorButtons($primaryActions, $translator);
            $this->addManageContentObjectButton($secondaryActions, $translator);
            $this->addNodeSpecificButtons($primaryActions, $secondaryActions);

            if ($this->get_action() != self::ACTION_REPORTING)
            {
                $this->addReportingButtons($tertiaryActions, $translator);
            }

            $buttonToolbar->addButtonGroup($primaryActions);
            $buttonToolbar->addButtonGroup($secondaryActions);
            $buttonToolbar->addButtonGroup($tertiaryActions);


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
        return parent::get_content_object_display_attachment_url(
            $attachment, $this->getCurrentLearningPathTreeNode()->getId()
        );
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addCreatorButtons($buttonGroup, $translator)
    {
        if ($this->canEditCurrentLearningPathTreeNode())
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
            $parameters[self::PARAM_CHILD_ID] = $this->getCurrentLearningPathTreeNode()->getId();

            $allowedTypes = $this->get_root_content_object()->get_allowed_types();

            $actionSelector = new LearningPathActionSelector(
                $this,
                $this->getUser()->getId(),
                $allowedTypes,
                $parameters,
                array(),
                'btn-primary'
            );

            /** @var ClassnameUtilities $classNameUtilities */
            $classNameUtilities = $this->getService('chamilo.libraries.architecture.classname_utilities');
            $firstItemContext = $classNameUtilities->getNamespaceParent(array_shift($allowedTypes), 3);
            $itemTranslation = $translator->getTranslation('TypeName', null, $firstItemContext);

            $actionButton = $actionSelector->getActionButton(
                $translator->getTranslation(
                    'CreateItem', array('ITEM' => lcfirst($itemTranslation)), Manager::context()
                ),
                new BootstrapGlyph('plus')
            );

            $buttonGroup->addButton($actionButton);

            $parameters[CreatorComponent::PARAM_CREATE_MODE] = CreatorComponent::CREATE_MODE_FOLDER;

            $folderSelector = new ActionSelector(
                $this, $this->getUser()->getId(), array(Section::class_name()), $parameters
            );

            $folderButton = $folderSelector->getActionButton(
                $translator->getTranslation('CreateFolder', null, Manager::context()),
                new BootstrapGlyph('plus')
            );

            $folderButton->addSubButton(new SubButtonDivider());
            $folderButton->addSubButton(
                new SubButton(
                    $translator->getTranslation('CopyFromOtherLearningPaths'),
                    new FontAwesomeGlyph('copy'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_COPY_SECTIONS))
                )
            );

            $buttonGroup->addButton($folderButton);
        }
    }

    /**
     * Adds the buttons to manage the current content object
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addManageContentObjectButton($buttonGroup, $translator)
    {
        if ($this->canEditCurrentLearningPathTreeNode())
        {
            $editTitle = $translator->getTranslation('UpdaterComponent', null, Manager::context());
            $editImage = new FontAwesomeGlyph('pencil');
            $editURL = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                )
            );

            $editButton = new SplitDropdownButton($editTitle, $editImage, $editURL);

            $this->addDeleteButton($editButton, $translator);
            $this->addMoveButton($editButton, $translator);
            $this->addBlockedStatusButton($editButton, $translator, $this->getCurrentLearningPathTreeNode());
            $this->addManageButton($editButton, $translator);

            $buttonGroup->addButton($editButton);
        }
    }

    /**
     * Adds the delete button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addDeleteButton($button, $translator)
    {
        if (!$this->getCurrentLearningPathTreeNode()->isRootNode() &&
            $this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()->getParentNode())
        )
        {
            $button->addSubButton(
                new SubButton(
                    $translator->getTranslation('DeleterComponent', null, Manager::context()),
                    new BootstrapGlyph('remove'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                        )
                    ),
                    SubButton::DISPLAY_ICON_AND_LABEL,
                    true
                )
            );
        }
    }

    /**
     * Adds the manage button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addManageButton($button, $translator)
    {
        if ($this->canEditCurrentLearningPathTreeNode())
        {
            if ($this->getCurrentLearningPathTreeNode()->hasChildNodes())
            {
                if (!$this->getCurrentLearningPathTreeNode()->isRootNode())
                {
                    $button->addSubButton(new SubButtonDivider());
                }

                $button->addSubButton(
                    new SubButton(
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
     * Adds a button to block / unblock the status of the given learning path tree node
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    protected function addBlockedStatusButton(
        SplitDropdownButton $button, Translation $translator, LearningPathTreeNode $learningPathTreeNode
    )
    {
        /** @var LearningPath $learningPath */
        $learningPath = $this->get_root_content_object();

        if (!$this->canEditCurrentLearningPathTreeNode()
            || $learningPath->enforcesDefaultTraversingOrder() || $learningPathTreeNode->isRootNode()
        )
        {
            return;
        }

        $translationVariable = ($learningPathTreeNode->getLearningPathChild() &&
            $learningPathTreeNode->getLearningPathChild()->isBlocked()) ?
            'MarkAsOptional' : 'MarkAsRequired';

        $icon = ($learningPathTreeNode->getLearningPathChild() &&
            $learningPathTreeNode->getLearningPathChild()->isBlocked()) ?
            'unlock' : 'ban';

        $moveButton = new SubButton(
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

        $button->addSubButton($moveButton);
    }

    /**
     * Adds a move button where you can directly select a parent / position to which you want to move the selected
     * item
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addMoveButton(SplitDropdownButton $button, Translation $translator)
    {
        if ($this->getCurrentLearningPathTreeNode()->isRootNode() ||
            !$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()->getParentNode())
        )
        {
            return;
        }

        $moveButton = new SubButton(
            $translator->getTranslation('Move', null, Manager::context()),
            new BootstrapGlyph('random'),
            '#',
            Button::DISPLAY_ICON_AND_LABEL,
            false,
            'mover-open'
        );

        $button->addSubButton($moveButton);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addReportingButtons($buttonGroup, $translator)
    {
        $label = $translator->getTranslation('MyProgress', null, Manager::context());

        $url = $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_REPORTING,
                self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
            )
        );

        $icon = new FontAwesomeGlyph('pie-chart');

        if (!$this->canEditCurrentLearningPathTreeNode())
        {
            $splitDropDownButton = new SplitDropdownButton($label, $icon, $url);
        }
        else
        {
            $splitDropDownButton = new SplitDropdownButton(
                $translator->getTranslation('Reporting', null, Manager::context()),
                new FontAwesomeGlyph('bar-chart'),
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS,
                        self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId(),
                    )
                )
            );

            $splitDropDownButton->addSubButton(new SubButton($label, $icon, $url));
        }

        $this->addActivityButton($splitDropDownButton, $translator);
        $this->addStudentViewButton($splitDropDownButton, $translator);
        $buttonGroup->addButton($splitDropDownButton);
    }

    /**
     * Adds the activity button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addActivityButton(SplitDropdownButton $button, $translator)
    {
        $extraButton = new SubButton(
            $translator->getTranslation('ActivityComponent', null, Manager::context()),
            new FontAwesomeGlyph('mouse-pointer'),
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_ACTIVITY,
                    self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                )
            )
        );

        $button->addSubButton($extraButton);
    }

    /**
     * Adds the activity button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addStudentViewButton(SplitDropdownButton $button, $translator)
    {
        $extraButton = new SubButton(
            $translator->getTranslation('ShowStudentView', null, Manager::context()),
            new FontAwesomeGlyph('user'),
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_SHOW_STUDENT_VIEW,
                    self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                )
            )
        );

        $button->addSubButton($extraButton);
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
