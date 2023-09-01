<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface;
use Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererModificationInterface;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * Class that provides functions to create a simple table with given data A simple table is like the name says a table
 * that is not as abstract as the sortable table Good for tables that don't have lots of data.
 * To use this simpletable
 * you need to provide the defaultProperties you want to view, and provide an array with objects of a dataclass. You
 * also need to provide a cellrenderer so it's easy to add actions to a table row
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke
 */
class SimpleTableRenderer
{
    protected SimpleTableCellRendererInterface $cellRenderer;

    protected Translator $translator;

    public function __construct(Translator $translator, SimpleTableCellRendererInterface $cellRenderer)
    {
        $this->translator = $translator;
        $this->cellRenderer = $cellRenderer;
    }

    /**
     * @throws \TableException
     */
    public function render(array $dataArray): string
    {
        $htmlTable = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $defaultProperties = $this->getCellRenderer()->getProperties();

        $this->buildTableHeader($htmlTable, $defaultProperties);
        $this->buildTableData($htmlTable, $dataArray, $defaultProperties);

        return $htmlTable->toHtml();
    }

    /**
     * Builds the table with given table data When a cellrenderer is available the system will add modification links
     * for each row
     *
     * @throws \TableException
     */
    public function buildTableData(
        HTML_Table $htmlTable, array $dataArray, array $defaultProperties
    ): void
    {
        $cellRenderer = $this->getCellRenderer();

        if (count($dataArray) > 0)
        {
            foreach ($dataArray as $data)
            {
                $contents = [];

                foreach ($defaultProperties as $index => $defaultproperty)
                {
                    $contents[] = $cellRenderer->renderCell($index, $data);
                }

                if ($cellRenderer instanceof SimpleTableCellRendererModificationInterface)
                {
                    $contents[] = $cellRenderer->getModificationLinks($data);
                }

                $htmlTable->addRow($contents);
            }
        }
        else
        {
            $rownumber =
                $htmlTable->addRow([$this->getTranslator()->trans('NoResults', [], StringUtilities::LIBRARIES)]);

            $htmlTable->setCellAttributes(
                $rownumber, 0,
                ['style' => '"font-style: italic;text-align:center;" colspan=' . count($cellRenderer->getProperties())]
            );
        }
    }

    /**
     * @throws \TableException
     */
    public function buildTableHeader(HTML_Table $htmlTable, array $defaultProperties): void
    {
        $cellrenderer = $this->getCellRenderer();
        $prefix = $cellrenderer->getPrefix();
        $namespace = $cellrenderer->getNamespace();
        $counter = 0;

        foreach ($defaultProperties as $defaultproperty)
        {
            if ($defaultproperty)
            {
                $htmlTable->setHeaderContents(
                    0, $counter, $this->getTranslator()->trans($prefix . $defaultproperty, [], $namespace)
                );
            }
            else
            {
                $htmlTable->setHeaderContents(0, $counter, '');
            }

            $counter ++;
        }

        if ($cellrenderer instanceof SimpleTableCellRendererModificationInterface)
        {
            $htmlTable->setHeaderContents(0, $counter, '');
        }
    }

    public function getCellRenderer(): SimpleTableCellRendererInterface
    {
        return $this->cellRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
