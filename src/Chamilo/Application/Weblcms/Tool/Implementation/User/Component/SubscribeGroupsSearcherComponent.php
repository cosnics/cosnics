<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup\UnsubscribedGroupTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * With this component the users can search through all the platform groups in a flast list without hierachy.
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubscribeGroupsSearcherComponent extends Manager implements TableSupport
{
    /**
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    /**
     * Renders the component
     *
     * @return string
     */
    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $table = new UnsubscribedGroupTable($this);

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds and returns the toolbar renderer
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

    /**
     * Returns the condition for the table
     *
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $properties = array(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME),
            new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION)
        );

        $searchCondition = $this->buttonToolbarRenderer->getConditions($properties);
        if ($searchCondition)
        {
            return $searchCondition;
        }

        return null;
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS)),
                Translation::getInstance()->getTranslation('SubscribeGroupsDetailsComponent', null, Manager::context())
            )
        );
    }
}