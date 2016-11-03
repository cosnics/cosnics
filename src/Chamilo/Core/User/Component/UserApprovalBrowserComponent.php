<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Table\Approval\UserApprovalTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: admin_user_browser.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * 
 * @package user.lib.user_manager.component
 */
class UserApprovalBrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_user_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        $table = new UserApprovalTable($this);
        
        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    public function get_table_condition($object_table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME), 
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME), 
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME), 
                '*' . $query . '*');
            return new OrCondition($or_conditions);
        }
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            
            $commonActions->addButton(
                new Button(
                    Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_url(), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_approval_browser');
    }
}
