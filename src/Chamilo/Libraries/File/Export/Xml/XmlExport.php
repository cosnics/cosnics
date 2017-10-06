<?php
namespace Chamilo\Libraries\File\Export\Xml;

use Chamilo\Libraries\File\Export\Export;

/**
 *
 * @package common.export.xml
 */

/**
 * Exports data to XML-format
 */
class XmlExport extends Export
{

    private $level = 0;
    const EXPORT_TYPE = 'xml';

    public function render_data()
    {
        $all = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $all .= str_repeat("\t", $this->level) . '<rootItem>' . "\n";
        $this->level ++;
        $all .= $this->write_array($this->get_data());
        $this->level --;
        $all .= str_repeat("\t", $this->level) . '</rootItem>' . "\n";
        return utf8_encode($all);
    }

    public function write_array($row)
    {
        foreach ($row as $key => $value)
        {
            if (is_numeric($key))
                $key = 'item';

            if (is_array($value))
            {
                $all .= str_repeat("\t", $this->level) . '<' . $key . '>' . "\n";
                $this->level ++;
                $all .= $this->write_array($value);
                $this->level --;
                $all .= str_repeat("\t", $this->level) . '</' . $key . '>' . "\n";
            }
            else
            {
                $all .= str_repeat("\t", $this->level) . '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
            }
        }
        return $all;
    }

    public function get_type()
    {
        return self::EXPORT_TYPE;
    }
}
