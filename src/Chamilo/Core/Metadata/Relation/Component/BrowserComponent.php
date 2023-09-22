<?php
namespace Chamilo\Core\Metadata\Relation\Component;

use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Relation\Manager;
use Chamilo\Core\Metadata\Relation\Table\RelationTableRenderer;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Metadata\Schema\Component
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
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

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

    /**
     * @throws \Exception
     */
    public function getRelationCondition(): ?Condition
    {
        return $this->buttonToolbarRenderer->getConditions(
            [new PropertyConditionVariable(Relation::class, Relation::PROPERTY_NAME)]
        );
    }

    public function getRelationTableRenderer(): RelationTableRenderer
    {
        return $this->getService(RelationTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(Relation::class, new DataClassCountParameters($this->getRelationCondition()));
        $relationTableRenderer = $this->getRelationTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $relationTableRenderer->getParameterNames(), $relationTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $relations = DataManager::retrieves(
            Relation::class, new DataClassRetrievesParameters(
                $this->getRelationCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $relationTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $relationTableRenderer->render($tableParameterValues, $relations);
    }
}
