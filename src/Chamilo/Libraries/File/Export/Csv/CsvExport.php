<?php
namespace Chamilo\Libraries\File\Export\Csv;

use Chamilo\Libraries\File\Export\Export;

/**
 * @package Chamilo\Libraries\File\Export\Csv
 */
class CsvExport extends Export
{

    public function render_data($data): string
    {
        $key_array = array_keys($data[0]);
        $all = implode(';', $key_array) . PHP_EOL;

        foreach ($data as $index => $row)
        {
            $all .= implode(';', $row) . PHP_EOL;
        }

        echo $all;

        return ($all);
    }
}
