<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioBookmarkSupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Menu;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class TabComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var \core\repository\content_object\portfolio\display\Menu
     */
    private $portfolio_menu;

    /**
     *
     * @var \libraries\format\DynamicVisualTabsRenderer
     */
    private $tabs_renderer;

    public function run()
    {
        $portfolio = $this->get_parent()->get_root_content_object();
        
        $trail = BreadcrumbTrail :: get_instance();
        
        if (! $portfolio)
        {
            return $this->display_error_page(Translation :: get('NoObjectSelected'));
        }
        
        $this->portfolio_menu = new Menu($this);
        
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
        
        $this->tabs_renderer = new DynamicVisualTabsRenderer('portfolio');
        
        if ($this->get_current_node()->is_root())
        {
            $view_title = $this->get_current_content_object()->get_title();
            $view_image = Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Home');
        }
        else
        {
            $view_title = Translation :: get('ViewerComponent');
            $view_image = Theme :: getInstance()->getImagePath(
                Manager :: package(), 
                'Tab/' . self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT);
        }
        
        $this->tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                $view_title, 
                $view_image, 
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                        self :: PARAM_STEP => $this->get_current_step())), 
                $this->get_action() == self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                false, 
                DynamicVisualTab :: POSITION_LEFT, 
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()) &&
             $this->get_current_node()->get_content_object()->has_right(RepositoryRights :: COLLABORATE_RIGHT))
        {
            if ($this->get_current_node()->is_root())
            {
                $edit_title = Translation :: get('ChangeIntroduction');
                $edit_image = Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Introducer');
            }
            else
            {
                $variable = $this->get_current_content_object() instanceof Portfolio ? 'UpdateFolder' : 'UpdaterComponent';
                
                $edit_title = Translation :: get($variable);
                $edit_image = Theme :: getInstance()->getImagePath(
                    Manager :: package(), 
                    'Tab/' . self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM);
            }
            
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                    $edit_title, 
                    $edit_image, 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                            self :: PARAM_STEP => $this->get_current_step())), 
                    $this->get_action() == self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                    false, 
                    DynamicVisualTab :: POSITION_LEFT, 
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        
        if ($this->get_parent()->is_allowed_to_view_feedback($this->get_current_node()) ||
             $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node()))
        {
            $title = Translation :: get('FeedbackComponent');
            
            if ($this->get_parent()->is_allowed_to_view_feedback($this->get_current_node()))
            {
                $feedback_count = $this->get_parent()->count_portfolio_feedbacks($this->get_current_node());
                
                if ($feedback_count > 0)
                {
                    $title .= ' [' . $feedback_count . ']';
                }
            }
            
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_FEEDBACK, 
                    $title, 
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Feedback'), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_FEEDBACK, 
                            self :: PARAM_STEP => $this->get_current_step())), 
                    $this->get_action() == self :: ACTION_FEEDBACK, 
                    false, 
                    DynamicVisualTab :: POSITION_LEFT, 
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        
        if ($this->get_parent() instanceof PortfolioBookmarkSupport && ! $this->get_parent()->is_own_portfolio())
        {
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_BOOKMARK, 
                    Translation :: get('BookmarkerComponent'), 
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_BOOKMARK), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_BOOKMARK, 
                            self :: PARAM_STEP => $this->get_current_step())), 
                    $this->get_action() == self :: ACTION_BOOKMARK, 
                    false, 
                    DynamicVisualTab :: POSITION_LEFT, 
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        
        $this->tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_ACTIVITY, 
                Translation :: get('ActivityComponent'), 
                Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_ACTIVITY), 
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_ACTIVITY, 
                        self :: PARAM_STEP => $this->get_current_step())), 
                $this->get_action() == self :: ACTION_ACTIVITY, 
                false, 
                DynamicVisualTab :: POSITION_LEFT, 
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        
        $additional_tabs = $this->get_parent()->get_portfolio_additional_tabs();
        
        foreach ($additional_tabs as $additional_tab)
        {
            $this->tabs_renderer->add_tab($additional_tab);
        }
        
        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            $variable = $this->get_current_content_object() instanceof Portfolio ? 'DeleteFolder' : 'DeleterComponent';
            
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, 
                    Translation :: get($variable), 
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(), 
                        'Tab/' . self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, 
                            self :: PARAM_STEP => $this->get_current_step())), 
                    $this->get_action() == self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, 
                    true, 
                    DynamicVisualTab :: POSITION_RIGHT, 
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            if ($this->get_current_content_object() instanceof Portfolio &&
                 count($this->get_current_node()->get_children()) > 1)
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_MANAGE, 
                        Translation :: get('ManagerComponent'), 
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MANAGE), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MANAGE, 
                                self :: PARAM_STEP => $this->get_current_step())), 
                        $this->get_action() == self :: ACTION_MANAGE, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
        }
        
        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            $variable = $this->get_current_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';
            
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_MOVE, 
                    Translation :: get($variable), 
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MOVE), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_MOVE, 
                            self :: PARAM_STEP => $this->get_current_step())), 
                    $this->get_action() == self :: ACTION_MOVE, 
                    false, 
                    DynamicVisualTab :: POSITION_RIGHT, 
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            if ($this->get_current_node()->get_content_object() instanceof Portfolio)
            {
                $template = \Chamilo\Core\Repository\Configuration :: registration_default_by_type(
                    ClassnameUtilities :: getInstance()->getNamespaceParent(Portfolio :: context(), 2));
                
                $selected_template_id = TypeSelector :: get_selection();
                
                $is_selected = ($this->get_action() == self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM &&
                     $selected_template_id != $template->get_id());
                
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                        Translation :: get('CreatorComponent'), 
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(), 
                            'Tab/' . self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                                self :: PARAM_STEP => $this->get_current_step())), 
                        $is_selected, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                
                $is_selected = ($this->get_action() == self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM &&
                     $selected_template_id == $template->get_id());
                
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                        Translation :: get('AddFolder'), 
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Folder'), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, 
                                self :: PARAM_STEP => $this->get_current_step(), 
                                TypeSelector :: PARAM_SELECTION => $template->get_id())), 
                        $is_selected, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
        }
        
        if ($this->get_parent() instanceof PortfolioComplexRights &&
             $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_USER, 
                    Translation :: get('UserComponent'), 
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_USER), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_USER, 
                            self :: PARAM_STEP => $this->get_current_step())), 
                    $this->get_action() == self :: ACTION_USER, 
                    false, 
                    DynamicVisualTab :: POSITION_RIGHT, 
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            
            if (! $this->get_parent()->get_portfolio_virtual_user() instanceof \Chamilo\Core\User\Storage\DataClass\User)
            {
                $variable = $this->get_current_content_object() instanceof Portfolio ? 'RightsFolder' : 'RightsComponent';
                
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_RIGHTS, 
                        Translation :: get($variable), 
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_RIGHTS), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_RIGHTS, 
                                self :: PARAM_STEP => $this->get_current_step())), 
                        $this->get_action() == self :: ACTION_RIGHTS, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
        }
        
        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()) &&
             $this->get_current_node()->has_siblings())
        {
            if (! $this->get_current_node()->is_last_child())
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_SORT, 
                        Translation :: get('MoveDown'), 
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(), 
                            'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_DOWN), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_SORT, 
                                self :: PARAM_SORT => self :: SORT_DOWN, 
                                self :: PARAM_STEP => $this->get_current_step())), 
                        $this->get_action() == self :: ACTION_SORT, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
            else
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_SORT, 
                        Translation :: get('MoveDownNotAvailable'), 
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(), 
                            'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_DOWN . 'Na'), 
                        null, 
                        false, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
            
            if (! $this->get_current_node()->is_first_child())
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_SORT, 
                        Translation :: get('MoveUp'), 
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(), 
                            'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_UP), 
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_SORT, 
                                self :: PARAM_SORT => self :: SORT_UP, 
                                self :: PARAM_STEP => $this->get_current_step())), 
                        $this->get_action() == self :: ACTION_SORT, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
            else
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_SORT, 
                        Translation :: get('MoveUpNotAvailable'), 
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(), 
                            'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_UP . 'Na'), 
                        null, 
                        false, 
                        false, 
                        DynamicVisualTab :: POSITION_RIGHT, 
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
        }
        
        return $this->build();
    }

    abstract function build();

    /**
     *
     * @see \libraries\SubManager::render_header()
     */
    public function render_header()
    {
        $html = array();
        
        $html = array();
        
        $html[] = parent :: render_header();
        $html[] = '<div style="width: 17%; float: left;">';
        
        if ($this->get_parent() instanceof PortfolioComplexRights &&
             $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $virtual_user = $this->get_parent()->get_portfolio_virtual_user();
            
            if ($virtual_user instanceof \Chamilo\Core\User\Storage\DataClass\User)
            {
                $revert_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER));
                $image_url = Theme :: getInstance()->getImagePath(Manager :: package(), 'Action/' . self :: ACTION_USER);
                
                $html[] = '<div class="portfolio-virtual-user">';
                $html[] = Translation :: get(
                    'ViewingPortfolioAsUser', 
                    array('USER' => $virtual_user->get_fullname(), 'URL' => $revert_url, 'IMAGE' => $image_url));
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
            }
        }
        
        $profilePhotoUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(), 
                Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE, 
                \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $this->get_root_content_object()->get_owner()->get_id()));
        
        $html[] = '<div class="portfolio-photo">';
        $html[] = '<img src="' . $profilePhotoUrl->getUrl() . '" />';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clear"></div>';
        $html[] = '<div style="width: 100%; overflow: auto;">';
        $html[] = $this->portfolio_menu->render_as_tree() . '<br /><br />';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div style="width: 81%; float: right; padding-left: 10px; min-height: 500px;">';
        
        $html[] = $this->get_tabs_renderer()->renderHeader();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::render_footer()
     */
    public function render_footer()
    {
        $html = array();
        
        $html[] = $this->get_tabs_renderer()->renderFooter();
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = parent :: render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Get the TabsRenderer
     * 
     * @return \libraries\format\DynamicVisualTabsRenderer
     */
    private function get_tabs_renderer()
    {
        return $this->tabs_renderer;
    }
}