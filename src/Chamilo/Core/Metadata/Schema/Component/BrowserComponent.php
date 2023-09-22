<?php
namespace Chamilo\Core\Metadata\Schema\Component;

use Chamilo\Core\Metadata\Schema\Manager;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Schema\Table\SchemaTableRenderer;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
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

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (!$this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

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
        $html = [];

        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderTable();

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
                    $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE])
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getSchemaTableRenderer(): SchemaTableRenderer
    {
        return $this->getService(SchemaTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $condition = $this->getButtonToolbarRenderer()->getConditions(
            [new PropertyConditionVariable(Schema::class, Schema::PROPERTY_NAME)]
        );

        $totalNumberOfItems = DataManager::count(Schema::class, new DataClassCountParameters($condition));
        $schemaTableRenderer = $this->getSchemaTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $schemaTableRenderer->getParameterNames(), $schemaTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $parameters = new DataClassRetrievesParameters(
            $condition, $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $schemaTableRenderer->determineOrderBy($tableParameterValues)
        );

        $schemas = DataManager::retrieves(Schema::class, $parameters);

        return $schemaTableRenderer->render($tableParameterValues, $schemas);
    }
}
