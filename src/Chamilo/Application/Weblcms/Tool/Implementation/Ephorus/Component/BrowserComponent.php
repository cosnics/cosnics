<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTableInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * Browser component for ephorus tool.
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager implements TableSupport, RequestTableInterface
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
        if ($object_table_class_name ==
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTable'
        )
        {
            $search_conditions = $this->buttonToolbarRenderer->getConditions(
                array(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
                )
            );

            $condition = new EqualityCondition(
                new PropertyConditionVariable(Request::class, Request::PROPERTY_COURSE_ID),
                new StaticConditionVariable($this->get_course_id())
            );
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
     * @param int $entryId
     *
     * @return string
     */
    public function get_ephorus_request_url($entryId)
    {
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_RESULT;
        $parameters[self::PARAM_CONTENT_OBJECT_IDS] = $entryId;

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
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $html = array();
            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
            $html[] = $this->buttonToolbarRenderer->render();

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
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get(
                        'AddDocument',
                        array(),
                        ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class)
                    ),
                    Theme::getInstance()->getCommonImagePath('Action/Add'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_PUBLISH_DOCUMENT
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
