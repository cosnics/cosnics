<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\SimpleTableCellRendererInterface;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\SimpleTableCellRendererModificationInterface;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Sven Vanpoucke
 */
class SimpleTableRenderer
{
    public function __construct(
        protected Translator $translator, protected SimpleTableCellRendererInterface $cellRenderer
    )
    {
    }

    /**
     * @throws \TableException
     */
    public function render(array $dataArray): string
    {
        $htmlTable = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $defaultProperties = $this->cellRenderer->getProperties();

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
        if (count($dataArray) > 0) {
            foreach ($dataArray as $data) {
                $contents = [];

                foreach ($defaultProperties as $index => $defaultproperty) {
                    $contents[] = $this->cellRenderer->renderCell($index, $data);
                }

                if ($this->cellRenderer instanceof SimpleTableCellRendererModificationInterface) {
                    $contents[] = $this->cellRenderer->getModificationLinks($data);
                }

                $htmlTable->addRow($contents);
            }
        }
        else {
            $rownumber = $htmlTable->addRow([$this->translator->trans('NoResults', [], StringUtilities::LIBRARIES)]);

            $htmlTable->setCellAttributes(
                $rownumber, 0, [
                    'style' => '"font-style: italic;text-align:center;" colspan=' .
                        count($this->cellRenderer->getProperties())
                ]
            );
        }
    }

    /**
     * @throws \TableException
     */
    public function buildTableHeader(HTML_Table $htmlTable, array $defaultProperties): void
    {
        $prefix = $this->cellRenderer->getPrefix();
        $namespace = $this->cellRenderer->getNamespace();
        $counter = 0;

        foreach ($defaultProperties as $defaultproperty) {
            if ($defaultproperty) {
                $htmlTable->setHeaderContents(
                    0, $counter, $this->translator->trans($prefix . $defaultproperty, [], $namespace)
                );
            }
            else {
                $htmlTable->setHeaderContents(0, $counter, '');
            }

            $counter ++;
        }

        if ($this->cellRenderer instanceof SimpleTableCellRendererModificationInterface) {
            $htmlTable->setHeaderContents(0, $counter, '');
        }
    }
}
