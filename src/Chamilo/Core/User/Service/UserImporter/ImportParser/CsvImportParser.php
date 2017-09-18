<?php

namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Parses an XML based user import file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CsvImportParser implements ImportParserInterface
{
    /**
     * @var StringUtilities
     */
    protected $stringUtilities;

    /**
     * CsvImportParser constructor.
     *
     * @param StringUtilities $stringUtilities
     */
    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * Parses an upload file into
     *
     * @param UploadedFile $file
     *
     * @return ImportUserData[]
     */
    public function parse(UploadedFile $file)
    {
        $importUsersData = array();
        $handle = fopen($file->getPathname(), "r");
        $keys = fgetcsv($handle, 1000, ";");

        for ($i = 0; $i < count($keys); $i ++)
        {
            $keys[$i] = (string) $this->stringUtilities->createString($keys[$i])->underscored();
        }

        while (($row_tmp = fgetcsv($handle, 1000, ";")) !== false)
        {
            $rowData = array();

            foreach ($row_tmp as $index => $value)
            {
                $rowData[$keys[$index]] = (trim($value));
            }

            $importUsersData[] = new ImportUserData(
                implode(';', $row_tmp), $rowData['action'], $rowData['username'], $rowData['firstname'],
                $rowData['lastname'],  $rowData['email'], $rowData['official_code'], $rowData['language'],
                $rowData['status'], $rowData['active'],  $rowData['phone'], $rowData['activation_date'],
                $rowData['expiration_date'], $rowData['auth_source'], $rowData['password']
            );
        }

        fclose($handle);

        return $importUsersData;
    }

    /**
     * Checks whether or not the current parser can parse the given file
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function canParseFile(UploadedFile $file)
    {
        $allowedMimeTypes = [
            'text/x-csv', 'text/csv', 'application/vnd.ms-excel', 'application/octet-stream',
            'application/force-download', 'text/comma-separated-values'
        ];

        return in_array($file->getClientMimeType(), $allowedMimeTypes);
    }
}