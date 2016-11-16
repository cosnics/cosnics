<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 * $Id: simple_table.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.html.table
 */

/**
 * Class that provides functions to create a simple table with given data A simple table is like the name says a table
 * that is not as abstract as the sortable table Good for tables that don't have lots of data.
 * To use this simpletable
 * you need to provide the defaultproperties you want to view, and provide an array with objects of a dataclass. You
 * also need to provide a cellrenderer so it's easy to add actions to a table row
 * 
 * @author Sven Vanpoucke
 */
class SimpleTable extends HTML_Table
{

    /**
     * Properties that will be showed
     */
    private $defaultproperties;

    /**
     * Data for the properties
     */
    private $data_array;

    /**
     * Cellrenderer for the table
     */
    private $cellrenderer;

    /**
     * Actionhandler for checkboxes
     */
    private $actionhandler;

    /**
     * Form used for actions
     */
    private $tableform;

    /**
     * Used for unique formname
     */
    private $tablename;

    /**
     * Constructor creates the table
     * 
     * @param Array $defaultproperties The properties you want to view in the list
     * @param Array $data_array A list of data classes, the system will use this to extract the property values from it
     * @param CellRenderer $cellrenderer Used for actions on each row
     */
    public function __construct($data_array, $cellrenderer, $actionhandler = null, $tablename)
    {
        parent::__construct(array('class' => 'table table-striped table-bordered table-hover table-responsive'));
        
        $this->defaultproperties = $cellrenderer->get_properties();
        $this->data_array = $data_array;
        $this->cellrenderer = $cellrenderer;
        $this->actionhandler = $actionhandler;
        $this->tablename = $tablename;
        
        if ($this->actionhandler)
            $this->tableform = new FormValidator($tablename);
        
        $this->build_table();
        $this->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);
        
        if ($this->actionhandler && $this->tableform->validate())
        {
            $this->actionhandler->handle_action($this->tableform->exportValues());
        }
    }

    /**
     * Builds the table with given parameters
     */
    public function build_table()
    {
        $this->build_table_header();
        $this->build_table_data();
        
        if ($this->actionhandler)
        {
            $this->tableform->addElement(
                'select', 
                'action', 
                Translation::get('Actions'), 
                $this->actionhandler->get_actions());
            $this->tableform->addElement(
                'submit', 
                'actionbutton', 
                Translation::get('Ok', null, Utilities::COMMON_LIBRARIES), 
                'class="submit"');
        }
    }

    /**
     * Builds the table header and if a cellrenderer is available it adds an extra column
     */
    public function build_table_header()
    {
        $counter = 0;
        
        if ($this->actionhandler)
        {
            $this->setHeaderContents(0, $counter, '');
            $counter ++;
        }
        
        foreach ($this->defaultproperties as $defaultproperty)
        {
            if (method_exists($this->cellrenderer, 'get_prefix'))
            {
                $prefix = $this->cellrenderer->get_prefix();
            }
            
            if ($defaultproperty)
                $this->setHeaderContents(0, $counter, Translation::get($prefix . $defaultproperty));
            else
                $this->setHeaderContents(0, $counter, '');
            
            $counter ++;
        }
        
        if (method_exists($this->cellrenderer, 'get_modification_links'))
        {
            $this->setHeaderContents(0, $counter, '');
        }
    }

    /**
     * Builds the table with given table data When a cellrenderer is available the system will add modification links
     * for each row
     */
    public function build_table_data()
    {
        $i = 0;
        
        if (count($this->data_array) > 0)
        {
            foreach ($this->data_array as $data)
            {
                $contents = array();
                
                if ($this->actionhandler)
                {
                    $element = $this->tableform->createElement('checkbox', 'id' . $data->get_id(), '');
                    $this->tableform->addElement($element);
                    $contents[] = '<div style="text-align: center;">' . $element->toHtml() . '</div>';
                }
                
                foreach ($this->defaultproperties as $index => $defaultproperty)
                {
                    $contents[] = $this->cellrenderer->render_cell($index, $data);
                }
                
                if (method_exists($this->cellrenderer, 'get_modification_links'))
                    $contents[] = $this->cellrenderer->get_modification_links($data);
                
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
                'style="font-style: italic;text-align:center;" colspan=' . $this->cellrenderer->get_property_count());
        }
    }

    public function toHTML()
    {
        $html = array();
        
        if ($this->actionhandler)
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
            
            $selectelement = $this->tableform->getElement('action');
            
            $parameters = "";
            foreach ($_GET as $name => $parameter)
                $parameters .= '&' . $name . '=' . $parameter;
            
            $html[] = '<br /><div style="float: left;"><a href="?' . $parameters .
                 '" onclick="select(true); return false;">Select All</a></div>';
            $html[] = '<div style="float: left;"> &nbsp; <a href="?' . $parameters .
                 '" onclick="select(false); return false;">Deselect All</a></div>';
            $html[] = '<div style="float: left;"> &nbsp; &nbsp; ' . $selectelement->toHtml() . '</div>';
            
            $submitelement = $this->tableform->getElement('actionbutton');
            $html[] = '<div> &nbsp; &nbsp; ' . $submitelement->toHtml() . '</div>';
            
            $html[] = '<input name="_qf__' . $this->tablename . '" type="hidden" value="" /></form>';
        }
        else
            $html[] = parent::toHTML();
        
        return implode(PHP_EOL, $html);
    }
}
