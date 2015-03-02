<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTableInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Browser component for ephorus tool.
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager implements TableSupport, RequestTableInterface
{

    private $action_bar;

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */

    /**
     * Runs this component and displays it's output
     */
    public function run()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * **************************************************************************************************************
     * Implemented functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the condition for the object table
     *
     * @param $object_table_class_name string
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        if ($object_table_class_name == __NAMESPACE__ . '\RequestTable')
        {
            $search_conditions = $this->action_bar->get_conditions(
                array(ContentObject :: PROPERTY_TITLE, ContentObject :: PROPERTY_DESCRIPTION));
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_COURSE_ID),
                new StaticConditionVariable($this->get_course_id()));
            if ($search_conditions != null)
            {
                $condition = new AndCondition(array($condition, $search_conditions));
            }

            return $condition;
        }
    }

    /**
     * Returns the url to the ephorus request component
     *
     * @param int $object_id
     *
     * @return string
     */
    public function get_ephorus_request_url($object_id)
    {
        $parameters[self :: PARAM_ACTION] = self :: ACTION_EPHORUS_REQUEST;
        $parameters[self :: PARAM_CONTENT_OBJECT_IDS] = $object_id;
        $parameters[\Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: ACTION_VIEW_RESULT;

        return $this->get_url($parameters);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns this component as html
     *
     * @return string
     */
    protected function as_html()
    {
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $html = array();
            $this->action_bar = $this->get_action_bar();
            $html[] = $this->action_bar->as_html();

            $table = new RequestTable($this);
            $html[] = $table->as_html();

            return implode(PHP_EOL, $html);
        }
        else
        {
            throw new NotAllowedException(false);
        }
    }

    /**
     * Returns the actionbar
     *
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get(
                    'AddDocument',
                    array(),
                    ClassnameUtilities :: getInstance()->getNamespaceFromClassname(self :: class_name())),
                Theme :: getInstance()->getCommonImagePath('action_add'),
                $this->get_url(
                    array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_PUBLISH_DOCUMENT)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
