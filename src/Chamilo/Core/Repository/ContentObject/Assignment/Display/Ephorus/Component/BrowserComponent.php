<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTableInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table\EntryRequest\EntryRequestTable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * Assignment browser component for ephorus tool.
 *
 * @author Tom Goethals - Hogeschool Gent
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
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        return $this->buttonToolbarRenderer->getConditions(
            array(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
            )
        );
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
     * @param int $entryId
     *
     * @return string
     */
    public function get_ephorus_request_url($entryId)
    {
        $parameters[self::PARAM_ACTION] = self::ACTION_CREATE;
        $parameters[self::PARAM_ENTRY_ID] = $entryId;
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


        $assignment = $this->getAssignment();

        $html[] = '<h3>' . Translation::get(
                'EphorusSubmissionsForAssignment',
                array(),
                ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class)
            ) . ': ' .
            $assignment->get_title() . '</h3>';
        $table = new EntryRequestTable($this);
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
}
