<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class provides some functions which can be used when importing data from external files into
 *
 * @package Chamilo\Libraries\File
 */
class Import
{

    /**
     * Reads a CSV-file into an array.
     * The first line of the CSV-file should contain the array-keys. Example:
     * FirstName;LastName;Email John;Doe;john.doe@mail.com Adam;Adams;adam@mail.com returns $result [0]['FirstName'] =
     * 'John'; $result [0]['LastName'] = 'Doe'; $result [0]['Email'] = 'john.doe@mail. com'; $result [1]['FirstName'] =
     * 'Adam'; ...
     *
     * @param string $filename
     * @return string[]
     */
    public function csv_to_array($filename)
    {
        $result = array();
        $handle = fopen($filename, "r");
        $keys = fgetcsv($handle, 1000, ";");

        for ($i = 0; $i < count($keys); $i ++)
        {
            $keys[$i] = (string) StringUtilities::getInstance()->createString($keys[$i])->underscored();
        }

        while (($row_tmp = fgetcsv($handle, 1000, ";")) !== FALSE)
        {

            $row = array();
            foreach ($row_tmp as $index => $value)
            {
                $row[$keys[$index]] = (trim($value));
            }
            $result[] = $row;
        }

        fclose($handle);

        return $result;
    }

    /**
     * This function will read the CSV-file and put it in an array.
     * This will happen without any use of key's
     *
     * @param string $filename
     * @return string[]
     */
    public function read_csv($filename)
    {
        $result = array();
        $handle = fopen($filename, "r");

        while (($row = fgetcsv($handle, 1000, ";")) !== FALSE)
        {
            if (! empty($row[0]))
            {
                $result[] = $row;
            }
        }

        fclose($handle);

        return $result;
    }
}
