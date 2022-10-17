<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PropertiesTable extends HTML_Table
{

    /**
     * @var string[]
     */
    private $properties;

    /**
     * Constructor creates the table
     *
     * @param string[] $properties
     */
    public function __construct(array $properties)
    {
        parent::__construct(
            ['class' => 'table table-striped table-bordered table-hover table-responsive']
        );
        $this->properties = $properties;

        $this->buildTable();
    }

    /**
     * Builds the table with given properties
     */
    public function buildTable()
    {
        if (count($this->properties) > 0)
        {
            foreach ($this->properties as $property => $values)
            {
                $contents = [];
                $contents[] = $property;

                if (!is_array($values))
                {
                    $values = [$values];
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

            $this->setColAttributes(0, ['class' => 'header', 'style' => 'vertical-align: middle;']);
        }
        else
        {
            $contents = [];
            $contents[] = Translation::get('NoResults', null, StringUtilities::LIBRARIES);
            $row = $this->addRow($contents);
            $this->setCellAttributes($row, 0, 'style="font-style: italic;text-align:center;" colspan=2');
        }
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string[] $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }
}

