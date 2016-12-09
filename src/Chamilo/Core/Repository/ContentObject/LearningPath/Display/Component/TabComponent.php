<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Menu;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

abstract class TabComponent extends Manager implements DelegateComponent
{

    private $learning_path_menu;

    public function get_prerequisites_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_BUILD_PREREQUISITES, 
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, 
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    public function get_mastery_score_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_SET_MASTERY_SCORE, 
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, 
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    public function get_configuration_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_CONFIGURE_FEEDBACK, 
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, 
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    public function run()
    {
        $learning_path = $this->get_parent()->get_root_content_object();
        
        $trail = BreadcrumbTrail::getInstance();
        
        if (! $learning_path)
        {
            return $this->display_error_page(Translation::get('NoObjectSelected'));
        }
        
        $this->learning_path_menu = new Menu(
            $this, 
            $this->get_complex_content_object_path(), 
            $this->get_parent()->get_learning_path_tree_menu_url(), 
            'learning-path-menu');
        
        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());
        
        foreach ($this->get_root_content_object()->get_complex_content_object_path()->get_parents_by_id(
            $this->get_current_step(), 
            true, 
            true) as $node_parent)
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_STEP] = $node_parent->get_id();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb($this->get_url($parameters), $node_parent->get_content_object()->get_title()));
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
        $isFullScreen = $this->getRequest()->query->get(self::PARAM_FULL_SCREEN, false);
        $isMenuHidden = Session::retrieve('learningPathMenuIsHidden');
        
        if ($isFullScreen)
        {
            Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        }
        
        $html = array();
        
        $html[] = parent::render_header();
        
        $html[] = '<div class="learning-path-display">';
        $html[] = '<iframe class="learning-path-display-full-screen" src="about:blank"></iframe>';
        
        $html[] = $this->get_navigation_bar();
        
        $html[] = '<div class="row">';
        
        // Menu
        
        $classes = array('col-xs-12', 'col-sm-3', 'col-lg-3', 'learning-path-tree-menu-container');
        
        if ($isMenuHidden == 'true')
        {
            $classes[] = 'learning-path-tree-menu-container-hidden';
        }
        else
        {
            $classes[] = 'learning-path-tree-menu-container-visible';
        }
        
        $html[] = '<div class="' . implode(' ', $classes) . '">';
        
        $html[] = '<h3>';
        $html[] = Translation::get('LearningPathNavigationMenu');
        $html[] = '</h3>';
        
        $html[] = '<div class="learning-path-tree-menu">';
        
        $html[] = $this->learning_path_menu->render();
        $html[] = '</div>';
        $html[] = '</div>';
        
        // Content
        
        $classes = array('col-xs-12', 'col-sm-9', 'col-lg-9', 'learning-path-content');
        
        if ($isMenuHidden == 'true')
        {
            $classes[] = 'learning-path-content-full-screen';
        }
        
        $html[] = '<div class="' . implode(' ', $classes) . '">';
        
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
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'LearningPathMenu.js');
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) .
                 'Plugin/Jquery/jquery.fullscreen.min.js');
        
        $html[] = parent::render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_learning_path_menu()
    {
        return $this->learning_path_menu;
    }

    /**
     * Retrieves the navigation menu for the learning path
     * 
     * @param $total_steps int
     * @param $current_step int
     * @param $current_content_object ContentObject
     */
    private function get_navigation_bar()
    {
        $current_node = $this->get_current_node();
        $html = array();
        
        if ($this->get_action() != self::ACTION_REPORTING || $this->is_current_step_set())
        {
            
            $html[] = '<div class="navbar-learning-path">';
            
            $html[] = '<div class="navbar-learning-path-actions">';
            $html[] = '<span class="learning-path-action-menu">';
            
            $previous_node = $current_node->get_previous();
            
            if ($previous_node instanceof ComplexContentObjectPathNode)
            {
                
                $previous_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                        self::PARAM_STEP => $previous_node->get_id()));
                
                $label = Translation::get('Previous');
                
                $html[] = '<a id="learning-path-navigate-left" href="' . $previous_url .
                     '"><span class="glyphicon glyphicon-arrow-left" alt="' . $label . '" title="' . $label .
                     '"></span></a>';
            }
            else
            {
                $label = Translation::get('PreviousNa');
                
                $html[] = '<span class="glyphicon glyphicon-arrow-left disabled" alt="' . $label . '" title="' . $label .
                     '"></span>';
            }
            
            $isMenuHidden = Session::retrieve('learningPathMenuIsHidden');
            
            $html[] = '<span class="glyphicon glyphicon-list-alt learning-path-action-menu-show' .
                 ($isMenuHidden != 'true' ? ' hidden' : '') . '"></span>';
            $html[] = '<span class="glyphicon glyphicon-list-alt learning-path-action-menu-hide' .
                 ($isMenuHidden == 'true' ? ' hidden' : '') . '"></span>';
            $html[] = '&nbsp;';
            $html[] = '<span class="glyphicon glyphicon-fullscreen learning-path-action-fullscreen"></span>';
            $html[] = '</span>';
            
            $next_node = $current_node->get_next();
            
            if ($next_node instanceof ComplexContentObjectPathNode)
            {
                $next_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                        self::PARAM_STEP => $next_node->get_id()));
                
                $label = Translation::get('Next');
                
                $html[] = '<a id="learning-path-navigate-right" href="' . $next_url .
                     '"><span class="glyphicon glyphicon-arrow-right" alt="' . $label . '" title="' . $label .
                     '"></span></a>';
            }
            else
            {
                $label = Translation::get('NextNa');
                
                $html[] = '<span class="glyphicon glyphicon-arrow-right disabled" alt="' . $label . '" title="' . $label .
                     '"></span>';
            }
            
            $html[] = '</div>';
            
            $html[] = '<div class="navbar-learning-path-progress">';
            $html[] = $this->get_progress_bar();
            $html[] = '</div>';
            
            $html[] = '</div>';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the progress bar for the learning path
     * 
     * @return array() HTML code of the progress bar
     */
    private function get_progress_bar()
    {
        $progress = $this->get_complex_content_object_path()->get_progress();
        
        return $this->render_progress_bar($progress);
    }

    /**
     *
     * @param integer $percent
     * @param integer $step
     *
     * @return string
     */
    private function render_progress_bar($percent, $step = 2)
    {
        $displayPercent = round($percent);
        
        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar progress-bar-striped progress-bar-info active" role="progressbar" aria-valuenow="' .
             $displayPercent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $displayPercent .
             '%; min-width: 2em;">';
        // $html[] = $displayPercent . '%';
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}