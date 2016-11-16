<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LinkItem;
use Chamilo\Core\Menu\Table\Item\ItemBrowserTable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent, TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->check_allowed();
        return $this->show_navigation_item_list();
    }

    public function show_navigation_item_list()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $this->parent = Request::get(self::PARAM_PARENT);
        
        $parameters = $this->get_parameters(true);
        
        $table = new ItemBrowserTable($this, $parameters, $this->get_condition());
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = '<div style="float: left; width: 12%; overflow:auto;">';
        $html[] = $this->get_menu()->render_as_tree();
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 85%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();
            
            $commonActions->addButton(
                new Button(
                    Translation::get('AddApplicationItem'), 
                    Theme::getInstance()->getImagePath('Chamilo\Core\Menu', 'Types/' . Item::TYPE_APPLICATION), 
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_CREATE, 
                            self::PARAM_TYPE => ApplicationItem::class_name())), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $commonActions->addButton(
                new Button(
                    Translation::get('AddCategoryItem'), 
                    Theme::getInstance()->getImagePath('Chamilo\Core\Menu', 'Types/' . Item::TYPE_CATEGORY), 
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_CREATE, 
                            self::PARAM_TYPE => CategoryItem::class_name())), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $commonActions->addButton(
                new Button(
                    Translation::get('AddLinkItem'), 
                    Theme::getInstance()->getImagePath('Chamilo\Core\Menu', 'Types/' . Item::TYPE_LINK), 
                    $this->get_url(
                        array(self::PARAM_ACTION => self::ACTION_CREATE, self::PARAM_TYPE => LinkItem::class_name())), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $setting = Configuration::getInstance()->get_setting(array('Chamilo\Core\Menu', 'enable_rights'));
            
            if ($setting == 1)
            {
                $toolActions->addButton(
                    new Button(
                        
                        Translation::get('Rights', null, Utilities::COMMON_LIBRARIES), 
                        Theme::getInstance()->getCommonImagePath('Action/Rights'), 
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_RIGHTS)), 
                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
            }
            
            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    public function get_condition()
    {
        $condition = null;
        $parent = (isset($this->parent) ? $this->parent : 0);
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT), 
            new StaticConditionVariable($parent));
        return $condition;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('menu_browser');
    }

    public function get_additional_parameters()
    {
        return array(Manager::PARAM_ITEM);
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
