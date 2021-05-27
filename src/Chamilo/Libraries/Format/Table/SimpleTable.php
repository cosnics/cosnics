<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 * Class that provides functions to create a simple table with given data A simple table is like the name says a table
 * that is not as abstract as the sortable table Good for tables that don't have lots of data.
 * To use this simpletable
 * you need to provide the defaultProperties you want to view, and provide an array with objects of a dataclass. You
 * also need to provide a cellrenderer so it's easy to add actions to a table row
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke
 */
class SimpleTable extends HTML_Table
{

    /**
     * Properties that will be showed
     *
     * @var
     *
     */
    private $defaultProperties;

    /**
     * Data for the properties
     *
     * @var string[][]
     */
    private $dataArray;

    /**
     * Cellrenderer for the table
     *
     * @var \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface
     */
    private $cellRenderer;

    /**
     * Used for unique formname
     *
     * @var string
     */
    private $tablename;

    /**
     *
     * @param string[][] $dataArray
     * @param \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface $cellrenderer
     * @param string $tablename
     */
    public function __construct($dataArray, $cellrenderer, $tablename)
    {
        parent::__construct(array('class' => 'table table-striped table-bordered table-hover table-responsive'));

        $this->defaultProperties = $cellrenderer->get_properties();
        $this->dataArray = $dataArray;
        $this->cellRenderer = $cellrenderer;
        $this->tablename = $tablename;

        $this->buildTable();
        $this->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        return parent::toHTML();
    }

    /**
     * Builds the table with given parameters
     */
    public function buildTable()
    {
        $this->buildTableHeader();
        $this->buildTableData();
    }

    /**
     * Builds the table with given table data When a cellrenderer is available the system will add modification links
     * for each row
     */
    public function buildTableData()
    {
        $i = 0;

        if (count($this->dataArray) > 0)
        {
            foreach ($this->dataArray as $data)
            {
                $contents = [];

                foreach ($this->defaultProperties as $index => $defaultproperty)
                {
                    $contents[] = $this->cellRenderer->render_cell($index, $data);
                }

                if (method_exists($this->cellRenderer, 'get_modification_links'))
                {
                    $contents[] = $this->cellRenderer->get_modification_links($data);
                }

                $this->addRow($contents);

                $i ++;
            }
        }
        else
        {
            $contents = [];
            $contents[] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
            $row = $this->addRow($contents);
            $this->setCellAttributes(
                $row, 0,
                'style="font-style: italic;text-align:center;" colspan=' . count($this->cellRenderer->get_properties())
            );
        }
    }

    /**
     * Builds the table header and if a cellrenderer is available it adds an extra column
     */
    public function buildTableHeader()
    {
        $counter = 0;

        foreach ($this->defaultProperties as $defaultproperty)
        {
            if (method_exists($this->cellRenderer, 'get_prefix'))
            {
                $prefix = $this->cellRenderer->get_prefix();
            }
            else
            {
                $prefix = '';
            }

            if ($defaultproperty)
            {
                $this->setHeaderContents(0, $counter, Translation::get($prefix . $defaultproperty));
            }
            else
            {
                $this->setHeaderContents(0, $counter, '');
            }

            $counter ++;
        }

        if (method_exists($this->cellRenderer, 'get_modification_links'))
        {
            $this->setHeaderContents(0, $counter, '');
        }
    }

    /**
     *
     * @return string
     * @deprecated User render() now
     */
    public function toHTML()
    {
        return $this->render();
    }
}
