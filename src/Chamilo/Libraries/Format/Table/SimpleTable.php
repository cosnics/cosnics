<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
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
     * Actionhandler for checkboxes
     *
     * @var object
     */
    private $actionHandler;

    /**
     * Form used for actions
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $tableForm;

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
     * @param object $actionhandler
     * @param string $tablename
     */
    public function __construct($dataArray, $cellrenderer, $actionhandler = null, $tablename)
    {
        parent::__construct(array('class' => 'table table-striped table-bordered table-hover table-responsive'));

        $this->defaultProperties = $cellrenderer->get_properties();
        $this->dataArray = $dataArray;
        $this->cellRenderer = $cellrenderer;
        $this->actionHandler = $actionhandler;
        $this->tablename = $tablename;

        if ($this->actionHandler)
            $this->tableForm = new FormValidator($tablename);

        $this->buildTable();
        $this->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);

        if ($this->actionHandler && $this->tableForm->validate())
        {
            $this->actionHandler->handle_action($this->tableForm->exportValues());
        }
    }

    /**
     * Builds the table with given parameters
     */
    public function buildTable()
    {
        $this->buildTableHeader();
        $this->buildTableData();

        if ($this->actionHandler)
        {
            $this->tableForm->addElement(
                'select',
                'action',
                Translation::get('Actions'),
                $this->actionHandler->get_actions());
            $this->tableForm->addElement(
                'submit',
                'actionbutton',
                Translation::get('Ok', null, Utilities::COMMON_LIBRARIES),
                'class="submit"');
        }
    }

    /**
     * Builds the table header and if a cellrenderer is available it adds an extra column
     */
    public function buildTableHeader()
    {
        $counter = 0;

        if ($this->actionHandler)
        {
            $this->setHeaderContents(0, $counter, '');
            $counter ++;
        }

        foreach ($this->defaultProperties as $defaultproperty)
        {
            if (method_exists($this->cellRenderer, 'get_prefix'))
            {
                $prefix = $this->cellRenderer->get_prefix();
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
                $contents = array();

                if ($this->actionHandler)
                {
                    $element = $this->tableForm->createElement('checkbox', 'id' . $data->get_id(), '');
                    $this->tableForm->addElement($element);
                    $contents[] = '<div style="text-align: center;">' . $element->toHtml() . '</div>';
                }

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
            $contents = array();
            $contents[] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
            $row = $this->addRow($contents);
            $this->setCellAttributes(
                $row,
                0,
                'style="font-style: italic;text-align:center;" colspan=' . $this->cellRenderer->get_property_count());
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

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        if ($this->actionHandler)
        {
            $html[] = '<form action="' . $this->action . '" method="post" name="' . $this->tablename . '" id="' .
                 $this->tablename . '">';
            $html[] = parent::toHTML();

            $html[] = '<script type="text/javascript">';
            $html[] = '  function select(bool)';
            $html[] = '  {';
            $html[] = '    var d = document["' . $this->tablename . '"];
								for (i = 0; i < d.elements.length; i++)
								{
									if (d.elements[i].type == "checkbox")
									{
									     d.elements[i].checked = bool;
									}
								}';
            $html[] = '  }';
            $html[] = '</script>';

            $selectelement = $this->tableForm->getElement('action');

            $parameters = "";
            foreach ($_GET as $name => $parameter)
            {
                $parameters .= '&' . $name . '=' . $parameter;
            }

            $html[] = '<br /><div style="float: left;"><a href="?' . $parameters .
                 '" onclick="select(true); return false;">Select All</a></div>';
            $html[] = '<div style="float: left;"> &nbsp; <a href="?' . $parameters .
                 '" onclick="select(false); return false;">Deselect All</a></div>';
            $html[] = '<div style="float: left;"> &nbsp; &nbsp; ' . $selectelement->toHtml() . '</div>';

            $submitelement = $this->tableForm->getElement('actionbutton');
            $html[] = '<div> &nbsp; &nbsp; ' . $submitelement->toHtml() . '</div>';

            $html[] = '<input name="_qf__' . $this->tablename . '" type="hidden" value="" /></form>';
        }
        else
        {
            $html[] = parent::toHTML();
        }

        return implode(PHP_EOL, $html);
    }
}
