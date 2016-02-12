<?php
namespace Chamilo\Core\Metadata\Provider\Component;

use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
// use Chamilo\Libraries\Storage\Query\Condition\InCondition;
// use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\Metadata\Provider\Table\ProviderLink\ProviderLinkTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Core\Metadata\Service\EntityConditionService;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
// use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
// use Chamilo\Core\Metadata\Service\EntityConditionService;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->verifySetup();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $html = array();

        $this->action_bar = $this->get_action_bar();
        $html[] = $this->action_bar->as_html();

        $table = new ProviderLinkTable($this);
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Configure', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Config'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE))));

        return $action_bar;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $entityConditionService = new EntityConditionService();
        $conditions = array();

        $entities = $this->getEntities();

        if (count($entities) > 0)
        {
            $conditions[] = $entityConditionService->getEntitiesCondition(
                $entities,
                ProviderLink :: class_name(),
                ProviderLink :: PROPERTY_ENTITY_TYPE);
        }

        return new AndCondition($conditions);
    }
}
