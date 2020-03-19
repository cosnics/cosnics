<?php
namespace Chamilo\Libraries\File\Export\Xml;

use Chamilo\Libraries\File\Export\Export;

/**
 * Exports data to XML-format
 *
 * @package Chamilo\Libraries\File\Export\Xml
 */
class XmlExport extends Export
{
    const EXPORT_TYPE = 'xml';

    /**
     *
     * @var integer
     */
    private $level = 0;

    /**
     *
     * @see \Chamilo\Libraries\File\Export\Export::get_type()
     */
    public function get_type()
    {
        return self::EXPORT_TYPE;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Export\Export::render_data()
     */
    public function render_data()
    {
        $all = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $all .= str_repeat("\t", $this->level) . '<rootItem>' . PHP_EOL;
        $this->level ++;
        $all .= $this->write_array($this->get_data());
        $this->level --;
        $all .= str_repeat("\t", $this->level) . '</rootItem>' . PHP_EOL;

        return utf8_encode($all);
    }

    /**
     *
     * @param string[] $row
     *
     * @return string
     */
    public function write_array($row)
    {
        $all = '';

        foreach ($row as $key => $value)
        {
            if (is_numeric($key))
            {
                $key = 'item';
            }

            if (is_array($value))
            {
                $all = str_repeat("\t", $this->level) . '<' . $key . '>' . PHP_EOL;
                $this->level ++;
                $all .= $this->write_array($value);
                $this->level --;
                $all .= str_repeat("\t", $this->level) . '</' . $key . '>' . PHP_EOL;
            }
            else
            {
                $all = str_repeat("\t", $this->level) . '<' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL;
            }
        }

        return $all;
    }
}
