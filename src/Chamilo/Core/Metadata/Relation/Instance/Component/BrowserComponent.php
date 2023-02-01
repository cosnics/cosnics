<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Component;

use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Core\Metadata\Relation\Instance\Table\RelationInstanceTableRenderer;
use Chamilo\Core\Metadata\Service\EntityConditionService;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Metadata\Relation\Instance\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->verifySetup();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->as_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function as_html(): string
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = [];

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->renderTable();

        return implode(PHP_EOL, $html);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans('Create', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('plus'), $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE])
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getEntityConditionService(): EntityConditionService
    {
        return $this->getService(EntityConditionService::class);
    }

    /**
     * @throws \Exception
     */
    public function getRelationCondition(): AndCondition
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

    public function getRelationInstanceTableRenderer(): RelationInstanceTableRenderer
    {
        return $this->getService(RelationInstanceTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(RelationInstance::class, new DataClassCountParameters($this->getRelationCondition()));
        $relationInstanceTableRenderer = $this->getRelationInstanceTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $relationInstanceTableRenderer->getParameterNames(),
            $relationInstanceTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $relations = DataManager::retrieves(
            RelationInstance::class, new DataClassRetrievesParameters(
                $this->getRelationCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(),
                $relationInstanceTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $relationInstanceTableRenderer->render($tableParameterValues, $relations);
    }
}
