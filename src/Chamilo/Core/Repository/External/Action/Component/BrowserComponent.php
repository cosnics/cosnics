<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Core\Repository\External\Action\Menu;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function render_menu()
    {
        $extra = $this->get_menu_items();
        if ($this->buttonToolbarRenderer->getSearchForm()->getQuery() && count($extra) > 0)
        {
            $search_url = '#';
            $search = array();
            
            $search['title'] = Translation::get('SearchResults');
            
            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra[] = $search;
        }
        else
        {
            $search_url = null;
        }
        
        $menu = new Menu(
            Request::get(\Chamilo\Core\Repository\External\Manager::PARAM_EXTERNAL_REPOSITORY_ID), 
            $this->get_parent(), 
            $extra);
        
        if ($search_url)
        {
            $menu->forceCurrentUrl($search_url);
        }
        
        $html = array();
        if ($menu->count_menu_items() > 0)
        {
            $html[] = '<div class="col-md-2">';
            $html[] = $menu->render_as_tree();
            $html[] = '</div>';
        }
        
        return implode(PHP_EOL, $html);
    }

    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        $html = array();
        
        if (isset($query) && $query != '')
        {
            $this->set_parameter(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $query);
        }
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        
        $html[] = '<div class="row">';
        
        if ($this->get_menu() == null)
        {
            $menu = $this->render_menu();
        }
        else
        {
            $menu = array();
            $menu[] = '<div class="col-md-2">';
            $menu[] = $this->get_menu()->render_as_tree();
            $menu[] = '</div>';
            $menu = implode(PHP_EOL, $menu);
        }
        
        if ($menu)
        {
            $html[] = $menu;
            $html[] = '<div class="col-md-10">';
        }
        
        $html[] = Renderer::factory($this->get_parent()->get_renderer(), $this)->as_html();
        
        if ($menu)
        {
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            return $this->translate_search_query($query);
        }
        
        return null;
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            
            $renderers = $this->get_parent()->get_available_renderers();
            
            if (count($renderers) > 1)
            {
                $currentRenderer = $this->get_parent()->get_renderer();
                
                $viewActions = new DropdownButton(
                    Translation::get($currentRenderer . 'View', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('View/' . $currentRenderer));
                $buttonToolbar->addItem($viewActions);
                
                foreach ($renderers as $renderer)
                {
                    if ($currentRenderer != $renderer)
                    {
                        $action = $this->get_url(
                            array(\Chamilo\Core\Repository\External\Manager::PARAM_RENDERER => $renderer));
                        $classes = '';
                    }
                    else
                    {
                        $action = '';
                        $classes = 'selected';
                    }
                    
                    $viewActions->addSubButton(
                        new SubButton(
                            Translation::get(
                                (string) StringUtilities::getInstance()->createString($renderer)->upperCamelize() .
                                     'View', 
                                    null, 
                                    Utilities::COMMON_LIBRARIES), 
                            Theme::getInstance()->getImagePath(
                                'Chamilo\Core\Repository', 
                                'View/' . StringUtilities::getInstance()->createString($renderer)->upperCamelize()), 
                            $action, 
                            Button::DISPLAY_LABEL, 
                            false, 
                            $classes));
                }
            }
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\External\Manager::PARAM_FOLDER);
    }
}
