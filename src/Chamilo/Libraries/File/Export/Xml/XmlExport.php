<?php
namespace Chamilo\Libraries\File\Export\Xml;

use Chamilo\Libraries\File\Export\Export;

/**
 * @package Chamilo\Libraries\File\Export\Xml
 */
class XmlExport extends Export
{
    public function serializeData($data): string
    {
        $level = 0;

        $all = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $all .= '<rootItem>' . PHP_EOL;
        $level ++;
        $all .= $this->write_array($level, $data);
        $all .= '</rootItem>' . PHP_EOL;

        return utf8_encode($all);
    }

    public function write_array(int $level, $row): string
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
                $all = str_repeat("\t", $level) . '<' . $key . '>' . PHP_EOL;
                $level ++;
                $all .= $this->write_array($level, $value);
                $level --;
                $all .= str_repeat("\t", $level) . '</' . $key . '>' . PHP_EOL;
            }
            else
            {
                $all = str_repeat("\t", $level) . '<' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL;
            }
        }

        return $all;
    }
}
