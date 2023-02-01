<?php
namespace Chamilo\Core\Metadata\Provider\Component;

use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Core\Metadata\Provider\Table\ProviderLinkTableRenderer;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
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
     * @throws \Exception
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
     * @return string
     * @throws \Exception
     */
    public function as_html(): string
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->getButtonToolbarRenderer()->render();
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
                    $this->getTranslator()->trans('Configure', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('cog'), $this->get_url([self::PARAM_ACTION => self::ACTION_CONFIGURE])
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
    public function getProviderLinkCondition(): AndCondition
    {
        $conditions = [];

        $entities = $this->getEntities();

        if (count($entities) > 0)
        {
            $conditions[] = $this->getEntityConditionService()->getEntitiesCondition(
                $entities, ProviderLink::class, ProviderLink::PROPERTY_ENTITY_TYPE
            );
        }

        return new AndCondition($conditions);
    }

    public function getProviderLinkTableRenderer(): ProviderLinkTableRenderer
    {
        return $this->getService(ProviderLinkTableRenderer::class);
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
            DataManager::count(ProviderLink::class, new DataClassCountParameters($this->getProviderLinkCondition()));
        $providerLinkTableRenderer = $this->getProviderLinkTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $providerLinkTableRenderer->getParameterNames(), $providerLinkTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $providerLinks = DataManager::retrieves(
            ProviderLink::class, new DataClassRetrievesParameters(
                $this->getProviderLinkCondition(),
                $tableParameterValues->getNumberOfItemsPerPage(),$tableParameterValues->getOffset(),
                $providerLinkTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $providerLinkTableRenderer->render($tableParameterValues, $providerLinks);
    }
}
