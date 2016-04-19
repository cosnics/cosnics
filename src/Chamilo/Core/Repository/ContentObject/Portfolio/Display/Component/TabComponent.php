<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Menu;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioBookmarkSupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Feedback\Generator\ActionsGenerator;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

abstract class TabComponent extends Manager implements DelegateComponent
{

    private $buttonToolBar;

    public function run()
    {
        $portfolio = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $portfolio)
        {
            return $this->display_error_page(Translation :: get('NoObjectSelected'));
        }

        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());

        foreach ($this->get_root_content_object()->get_complex_content_object_path()->get_parents_by_id(
            $this->get_current_step(),
            true,
            true) as $node_parent)
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_STEP] = $node_parent->get_id();
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb($this->get_url($parameters), $node_parent->get_content_object()->get_title()));
        }

        $this->buttonToolBar = new ButtonToolBar();

        $contentItems = new ButtonGroup();
        $this->buttonToolBar->addItem($contentItems);

        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            if ($this->get_current_node()->get_content_object() instanceof Portfolio)
            {

                $parameters = $this->get_parameters();
                $parameters[self :: PARAM_ACTION] = self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
                $parameters[self :: PARAM_STEP] = $this->get_current_step();

                $contentItems->addButton(
                    $this->getPublicationButton(
                        Translation :: get('CreatorComponent'),
                        new BootstrapGlyph('plus'),
                        $this->get_root_content_object()->get_allowed_types(),
                        $parameters,
                        array(),
                        'btn-primary'));

                // $template = \Chamilo\Core\Repository\Configuration :: registration_default_by_type(
                // ClassnameUtilities :: getInstance()->getNamespaceParent(Portfolio :: context(), 2));

                // $parameters = $this->get_parameters();
                // $parameters[self :: PARAM_ACTION] = self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
                // $parameters[self :: PARAM_STEP] = $this->get_current_step();
                // $parameters[TypeSelector :: PARAM_SELECTION] = $template->get_id();

                // $contentItems->addButton(
                // $this->getPublicationButton(
                // Translation :: get('AddFolder'),
                // new BootstrapGlyph('folder-open'),
                // array(Portfolio :: class_name()),
                // $parameters));
            }
        }

        $canEditNodeOrContentObject = $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()) && RightsService :: getInstance()->canEditContentObject(
            $this->get_user(),
            $this->get_current_content_object());

        $canMoveNode = ! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent());

        if ($canEditNodeOrContentObject || $canMoveNode)
        {
            if ($canEditNodeOrContentObject && $canMoveNode)
            {
                if ($this->get_current_node()->is_root())
                {
                    $editTitle = Translation :: get('ChangeIntroduction');
                    $editImage = new FontAwesomeGlyph('compass');
                }
                else
                {
                    $variable = $this->get_current_content_object() instanceof Portfolio ? 'UpdateFolder' : 'UpdaterComponent';

                    $editTitle = Translation :: get($variable);
                    $editImage = new FontAwesomeGlyph('pencil');
                }

                $editButton = new SplitDropdownButton(
                    $editTitle,
                    $editImage,
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self :: PARAM_STEP => $this->get_current_step())));

                $variable = $this->get_current_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';

                if ($this->get_current_node()->has_siblings())
                {
                    $editButton->addSubButton(
                        new SubButton(
                            Translation :: get('MoveToOtherFolder'),
                            new BootstrapGlyph('folder-open'),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_MOVE,
                                    self :: PARAM_STEP => $this->get_current_step()))));

                    if (! $this->get_current_node()->is_first_child())
                    {
                        $editButton->addSubButton(
                            new SubButton(
                                Translation :: get('MoveUp'),
                                new BootstrapGlyph('chevron-up'),
                                $this->get_url(
                                    array(
                                        self :: PARAM_ACTION => self :: ACTION_SORT,
                                        self :: PARAM_SORT => self :: SORT_UP,
                                        self :: PARAM_STEP => $this->get_current_step()))));
                    }

                    if (! $this->get_current_node()->is_last_child())
                    {
                        $editButton->addSubButton(
                            new SubButton(
                                Translation :: get('MoveDown'),
                                new BootstrapGlyph('chevron-down'),
                                $this->get_url(
                                    array(
                                        self :: PARAM_ACTION => self :: ACTION_SORT,
                                        self :: PARAM_SORT => self :: SORT_DOWN,
                                        self :: PARAM_STEP => $this->get_current_step()))));
                    }
                }
                else
                {
                    $editButton->addSubButton(
                        new SubButton(
                            Translation :: get('MoveToOtherFolder'),
                            new BootstrapGlyph('folder-open'),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_MOVE,
                                    self :: PARAM_STEP => $this->get_current_step()))));
                }

                $contentItems->addButton($editButton);
            }
            elseif (! $canEditNodeOrContentObject && $canMoveNode)
            {
                $variable = $this->get_current_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';

                if ($this->get_current_node()->has_siblings())
                {
                    $moveButton = new DropdownButton(Translation :: get($variable), new BootstrapGlyph('sort'));

                    $moveButton->addSubButton(
                        new SubButton(
                            Translation :: get('MoveToOtherFolder'),
                            new BootstrapGlyph('folder-open'),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_MOVE,
                                    self :: PARAM_STEP => $this->get_current_step()))));

                    if (! $this->get_current_node()->is_first_child())
                    {
                        $moveButton->addSubButton(
                            new SubButton(
                                Translation :: get('MoveUp'),
                                new BootstrapGlyph('chevron-up'),
                                $this->get_url(
                                    array(
                                        self :: PARAM_ACTION => self :: ACTION_SORT,
                                        self :: PARAM_SORT => self :: SORT_UP,
                                        self :: PARAM_STEP => $this->get_current_step()))));
                    }

                    if (! $this->get_current_node()->is_last_child())
                    {
                        $moveButton->addSubButton(
                            new SubButton(
                                Translation :: get('MoveDown'),
                                new BootstrapGlyph('chevron-down'),
                                $this->get_url(
                                    array(
                                        self :: PARAM_ACTION => self :: ACTION_SORT,
                                        self :: PARAM_SORT => self :: SORT_DOWN,
                                        self :: PARAM_STEP => $this->get_current_step()))));
                    }

                    $contentItems->addButton($moveButton);
                }
                else
                {
                    $contentItems->addButton(
                        new Button(
                            Translation :: get($variable),
                            Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MOVE),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_MOVE,
                                    self :: PARAM_STEP => $this->get_current_step()))));
                }
            }
            else
            {
                if ($this->get_current_node()->is_root())
                {
                    $editTitle = Translation :: get('ChangeIntroduction');
                    $editImage = new FontAwesomeGlyph('compass');
                }
                else
                {
                    $variable = $this->get_current_content_object() instanceof Portfolio ? 'UpdateFolder' : 'UpdaterComponent';

                    $editTitle = Translation :: get($variable);
                    $editImage = new FontAwesomeGlyph('pencil');
                }

                $contentItems->addButton(
                    new Button(
                        $editTitle,
                        $editImage,
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                                self :: PARAM_STEP => $this->get_current_step())),
                        Button :: DISPLAY_ICON_AND_LABEL));
            }
        }

        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            $variable = $this->get_current_content_object() instanceof Portfolio ? 'DeleteFolder' : 'DeleterComponent';

            $contentItems->addButton(
                new Button(
                    Translation :: get($variable),
                    new BootstrapGlyph('remove'),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self :: PARAM_STEP => $this->get_current_step())),
                    Button :: DISPLAY_ICON_AND_LABEL,
                    true));
        }

        if ($this->get_parent() instanceof PortfolioComplexRights &&
             $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            if (! $this->get_parent()->get_portfolio_virtual_user() instanceof \Chamilo\Core\User\Storage\DataClass\User)
            {
                $variable = $this->get_current_content_object() instanceof Portfolio ? 'RightsFolder' : 'RightsComponent';

                $rightsButton = new SplitDropdownButton(
                    Translation :: get($variable),
                    new BootstrapGlyph('lock'),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_RIGHTS,
                            self :: PARAM_STEP => $this->get_current_step())));

                $rightsButton->addSubButton(
                    new SubButton(
                        Translation :: get('UserComponent'),
                        new BootstrapGlyph('user'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_USER,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }
            else
            {

                $rightsButton = new Button(
                    Translation :: get('UserComponent'),
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_USER),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_USER,
                            self :: PARAM_STEP => $this->get_current_step())));
            }

            $this->buttonToolBar->addItem($rightsButton);
        }

        $this->buttonToolBar->addItem(new ButtonGroup($this->get_parent()->get_portfolio_additional_actions()));
        $this->buttonToolBar->addItem($this->getExtraButton());

        return $this->build();
    }

    public function getExtraButton()
    {
        $extraButton = new DropdownButton(Translation :: get('Extra'), new BootstrapGlyph('cog'));

        $extraButton->addSubButton(
            new SubButton(
                Translation :: get('ActivityComponent'),
                new FontAwesomeGlyph('mouse-pointer'),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_ACTIVITY,
                        self :: PARAM_STEP => $this->get_current_step()))));

        $extraButton->addSubButtons($this->getFeedbackSubButtons());

        $areBookmarksAllowed = $this->get_parent() instanceof PortfolioBookmarkSupport &&
             ! $this->get_parent()->is_own_portfolio();

        if ($areBookmarksAllowed)
        {
            $extraButton->addSubButton(
                new SubButton(
                    Translation :: get('BookmarkerComponent'),
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_BOOKMARK),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_BOOKMARK,
                            self :: PARAM_STEP => $this->get_current_step()))));
        }

        $isManagingAllowed = $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node());

        if ($isManagingAllowed)
        {
            if ($this->get_current_content_object() instanceof Portfolio &&
                 count($this->get_current_node()->get_children()) > 1)
            {
                $extraButton->addSubButton(
                    new SubButton(
                        Translation :: get('ManagerComponent'),
                        new BootstrapGlyph('cog'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MANAGE,
                                self :: PARAM_STEP => $this->get_current_step()))));
            }
        }

        return $extraButton;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getFeedbackSubButtons()
    {
        $isAllowedToViewFeedback = $this->get_parent()->is_allowed_to_view_feedback($this->get_current_node());
        $isAllowedToCreateFeedback = $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node());

        if ($isAllowedToViewFeedback || $isAllowedToCreateFeedback)
        {
            $baseParameters = array(
                self :: PARAM_ACTION => self :: ACTION_FEEDBACK,
                self :: PARAM_STEP => $this->get_current_step());

            if ($isAllowedToViewFeedback)
            {
                $feedbackCount = $this->get_parent()->count_portfolio_feedbacks($this->get_current_node());
                $portfolioNotification = $this->get_parent()->retrieve_portfolio_notification($this->get_current_node());
                $hasNotification = $portfolioNotification instanceof Notification;
            }
            else
            {
                $feedbackCount = 0;
                $hasNotification = false;
            }

            $actionsGenerator = new ActionsGenerator(
                $this,
                $baseParameters,
                $isAllowedToViewFeedback,
                $feedbackCount,
                $hasNotification);

            return $actionsGenerator->run();
        }
    }

    abstract function build();

    /**
     *
     * @see \libraries\SubManager::render_header()
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = '<div class="col-md-3 col-lg-2 col-sm-12">';

        if ($this->get_parent() instanceof PortfolioComplexRights &&
             $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $virtual_user = $this->get_parent()->get_portfolio_virtual_user();

            if ($virtual_user instanceof \Chamilo\Core\User\Storage\DataClass\User)
            {
                $revert_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER));
                $image_url = Theme :: getInstance()->getImagePath(Manager :: package(), 'Action/' . self :: ACTION_USER);

                $html[] = '<div class="alert alert-warning">';
                $html[] = Translation :: get(
                    'ViewingPortfolioAsUser',
                    array('USER' => $virtual_user->get_fullname(), 'URL' => $revert_url, 'IMAGE' => $image_url));
                $html[] = '</div>';
            }
        }

        $profilePhotoUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $this->get_root_content_object()->get_owner()->get_id()));

        // User photo
        $html[] = '<div class="panel panel-default panel-portfolio">';
        $html[] = '<div class="panel-body">';
        $html[] = '<img src="' . $profilePhotoUrl->getUrl() . '" class="portfolio-photo" />';
        $html[] = '</div>';
        $html[] = '</div>';

        // Tree menu
        $portfolioMenu = new Menu($this);

        $html[] = '<div class="clearfix"></div>';
        $html[] = '<div class="portfolio-tree-menu">';
        $html[] = $portfolioMenu->render_as_tree();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-md-9 col-lg-10 col-sm-12">';

        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->buttonToolBar);

        $html[] = $buttonToolBarRenderer->render();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::render_footer()
     */
    public function render_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $label
     * @param unknown $glyph
     * @param unknown $allowedContentObjectTypes
     * @param string[] $parameters
     * @param array $extraActions
     * @param unknown $classes
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton
     */
    public function getPublicationButton($label, $glyph, $allowedContentObjectTypes, $parameters,
        $extraActions = array(), $classes = null)
    {
        $actionSelector = new ActionSelector(
            $this,
            $this->getUser()->getId(),
            $allowedContentObjectTypes,
            $parameters,
            $extraActions,
            $classes);

        return $actionSelector->getActionButton($label, $glyph);
    }
}