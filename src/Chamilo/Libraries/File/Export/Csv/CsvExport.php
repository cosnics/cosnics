<?php
namespace Chamilo\Libraries\File\Export\Csv;

use Chamilo\Libraries\File\Export\Export;

/**
 *
 * @package common.export.csv
 */
/**
 * Exports data to CSV-format
 */
class CsvExport extends Export
{
    const EXPORT_TYPE = 'csv';

    public function render_data()
    {
        $data = $this->get_data();
        $key_array = array_keys($data[0]);
        $all = implode(';', $key_array) . "\n";
        foreach ($data as $index => $row)
        {
            $all .= implode(';', $row) . "\n";
        }
        return ($all);
    }

    public function get_type()
    {
        return self::EXPORT_TYPE;
    }
}
