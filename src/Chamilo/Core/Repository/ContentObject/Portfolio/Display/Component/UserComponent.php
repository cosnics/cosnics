<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\User\UserTable;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows a user to emulate the rights another user has on his or her portfolio
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserComponent extends TabComponent implements TableSupport
{

    /**
     *
     * @var \libraries\format\ActionBarRenderer
     */
    private $action_bar;

    /**
     * Executes this component
     */
    public function build()
    {

        // Check whether portfolio rights are enabled and whether the user can actually set them
        if (! $this->get_parent() instanceof PortfolioComplexRights ||
             ! $this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $message = Display :: warning_message(Translation :: get('ComplexRightsNotSupported'));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $message;
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        // If a virtual user is currently configured, clear it
        $virtual_user = $this->get_parent()->get_portfolio_virtual_user();

        if ($virtual_user instanceof \Chamilo\Core\User\Storage\DataClass\User)
        {
            $this->get_parent()->clear_virtual_user_id();
            $this->redirect(
                Translation :: get('BackInRegularView'),
                false,
                array(self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT));
        }

        $this->set_parameter(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, $this->get_action_bar()->get_query());

        // Handle a virtual user selection
        $selected_virtual_user_id = Request :: get(self :: PARAM_VIRTUAL_USER_ID);

        if ($selected_virtual_user_id)
        {
            if (! $this->get_parent()->set_portfolio_virtual_user_id($selected_virtual_user_id))
            {
                $this->redirect(Translation :: get('ImpossibleToViewAsSelectedUser'), true);
            }
            else
            {
                $this->redirect(
                    Translation :: get('ViewingPortfolioAsSelectedUser'),
                    false,
                    array(self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT));
            }
        }

        // Default table of users which can be emulated (as determined by the context)
        $table = new UserTable($this);

        $html = array();
        $html[] = $this->get_action_bar()->as_html();
        $html[] = $table->as_html();
        
        $axtionBar = implode(PHP_EOL, $html);
        
        $html = array();

        $html[] = $this->render_header();
        $html[] = $axtionBar;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $properties = array();
        $properties[] = new PropertyConditionVariable(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_FIRSTNAME);
        $properties[] = new PropertyConditionVariable(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_LASTNAME);
        $properties[] = new PropertyConditionVariable(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_OFFICIAL_CODE);

        return $this->get_action_bar()->get_conditions($properties);
    }

    /**
     * Get the component actionbar
     *
     * @return \libraries\format\ActionBarRenderer
     */
    public function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $this->action_bar->set_search_url($this->get_url());

            $this->action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $this->action_bar;
    }
}
