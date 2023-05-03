<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\GlossaryRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * Class to render the glossary as a table
 *
 * @package repository\content_object\glossary
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class TableGlossaryRenderer extends GlossaryRenderer implements TableSupport
{
    /**
     * @return string
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function render()
    {
        $totalNumberOfItems = $this->get_component()->count_objects();
        $glossayViewerTableRenderer = $this->getGlossayViewerTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $glossayViewerTableRenderer->getParameterNames(), $glossayViewerTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $glossayItems = $this->get_component()->get_objects(
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $glossayViewerTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $glossayViewerTableRenderer->legacyRender($this, $tableParameterValues, $glossayItems);
    }

    public function getGlossayViewerTableRenderer(): GlossayViewerTableRenderer
    {
        return $this->getService(GlossayViewerTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_parameters()
     */

    public function get_parameters()
    {
        return $this->get_component()->get_parameters();
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return null;
    }
}