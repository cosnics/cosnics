<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Component;

use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Core\Metadata\Relation\Instance\Table\Relation\RelationTable;
use Chamilo\Core\Metadata\Service\EntityConditionService;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->verifySetup();

        $html = [];

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
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = [];

        $html[] = $this->buttonToolbarRenderer->render();

        $table = new RelationTable($this);
        $html[] = $table->render();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
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
                    Translation::get('Create', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE))
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityConditionService
     */
    public function getEntityConditionService()
    {
        return $this->getService(EntityConditionService::class);
    }

    /**
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @throws \Exception
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = [];

        $relations = $this->getRelations();

        if (count($relations) > 0)
        {
            $relationIdentifiers = [];

            foreach ($relations as $relation)
            {
                $relationIdentifiers[] = $relation->getId();
            }

            $conditions[] = new InCondition(
                new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_RELATION_ID),
                $relationIdentifiers
            );
        }

        $sourceEntities = $this->getSourceEntities();

        if (count($sourceEntities) > 0)
        {
            $conditions[] = $this->getEntityConditionService()->getEntitiesCondition(
                $sourceEntities, RelationInstance::class, RelationInstance::PROPERTY_SOURCE_TYPE,
                RelationInstance::PROPERTY_SOURCE_ID
            );
        }

        $targetEntities = $this->getTargetEntities();

        if (count($targetEntities) > 0)
        {
            $conditions[] = $this->getEntityConditionService()->getEntitiesCondition(
                $targetEntities, RelationInstance::class, RelationInstance::PROPERTY_TARGET_TYPE,
                RelationInstance::PROPERTY_TARGET_ID
            );
        }

        return new AndCondition($conditions);
    }
}
