<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment\AssignmentRequestTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTableInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Assignment browser component for ephorus tool.
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class AssignmentBrowserComponent extends Manager implements TableSupport, RequestTableInterface
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

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
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            throw new NotAllowedException(false);
        }
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
        if ($object_table_class_name ==
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment\AssignmentRequestTable'
        )
        {
            $search_conditions = $this->buttonToolbarRenderer->getConditions(
                array(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE),
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
                )
            );

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    AssignmentSubmission::class_name(),
                    AssignmentSubmission::PROPERTY_PUBLICATION_ID
                ),
                new StaticConditionVariable($this->get_publication_id())
            );
            if ($search_conditions != null)
            {
                $condition = new AndCondition(array($condition, $search_conditions));
            }

            return $condition;
        }
    }

    public function get_publication_id()
    {
        return \Chamilo\Libraries\Platform\Session\Request::get(
            \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION
        );
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
        $parameters[self::PARAM_ACTION] = self::ACTION_EPHORUS_REQUEST;
        $parameters[self::PARAM_CONTENT_OBJECT_IDS] = $object_id;
        $parameters[\Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::ACTION_VIEW_RESULT;

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
        $html = array();

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html[] = $this->buttonToolbarRenderer->render();

        $pub = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        );

        $assignment = $pub->get_content_object();

        $html[] = '<h3>' . Translation::get(
                'EphorusSubmissionsForAssignment',
                array(),
                ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class_name())
            ) . ': ' .
            $assignment->get_title() . '</h3>';
        $table = new AssignmentRequestTable($this);
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the actionbar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
    }
}
