<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

class PropertiesTable extends HTML_Table
{

    private $properties;

    /**
     * Constructor creates the table
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        parent :: __construct(array('class' => 'data_table data_table_no_header'));
        $this->properties = $properties;

        $this->build_table();

        // $this->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);
    }

    /**
     * Builds the table with given properties
     */
    public function build_table()
    {
        if (count($this->properties) > 0)
        {
            foreach ($this->properties as $property => $values)
            {
                $contents = array();
                $contents[] = $property;

                if (! is_array($values))
                {
                    $values = array($values);
                }

                if (count($values) > 0)
                {
                    foreach ($values as $value)
                    {
                        $contents[] = $value;
                    }
                }

                $this->addRow($contents);
            }

            $this->setColAttributes(0, array('class' => 'header'));
        }
        else
        {
            $contents = array();
            $contents[] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
            $row = $this->addRow($contents);
            $this->setCellAttributes($row, 0, 'style="font-style: italic;text-align:center;" colspan=2');
        }
    }
}
