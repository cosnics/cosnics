<?php
namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Domain\UserImporter\UserImporterResult;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Parses an XML based user import file
 *
 * @package Chamilo\Core\User\Service\UserImporter\ImportParser
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CsvImportParser implements ImportParserInterface
{
    protected StringUtilities $stringUtilities;

    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * Checks whether or not the current parser can parse the given file
     */
    public function canParseFile(UploadedFile $file): bool
    {
        $allowedMimeTypes = [
            'text/x-csv',
            'text/csv',
            'application/vnd.ms-excel',
            'application/octet-stream',
            'application/force-download',
            'text/comma-separated-values'
        ];

        return in_array($file->getClientMimeType(), $allowedMimeTypes);
    }

    /**
     * Parses an upload file into
     *
     * @return \Chamilo\Core\User\Domain\UserImporter\ImportUserData[]
     */
    public function parse(UploadedFile $file, UserImporterResult $userImporterResult): array
    {
        $importUsersData = [];
        $handle = fopen($file->getPathname(), 'r');
        $keys = fgetcsv($handle, 1000, ';');

        if ($keys !== false)
        {
            for ($i = 0; $i < count($keys); $i ++)
            {
                $keys[$i] = $this->stringUtilities->createString($keys[$i])->underscored()->toString();
            }

            $userImporterResult->setRawImportDataHeader(implode(';', $keys));

            while (($row_tmp = fgetcsv($handle, 1000, ';')) !== false)
            {
                $rowData = [];

                foreach ($row_tmp as $index => $value)
                {
                    $rowData[$keys[$index]] = (trim($value));
                }

                $importUsersData[] = new ImportUserData(
                    implode(';', $row_tmp), $rowData['action'], $rowData['username'], $rowData['firstname'],
                    $rowData['lastname'], $rowData['email'], $rowData['official_code'], $rowData['language'],
                    $rowData['status'], $rowData['active'], $rowData['phone'], $rowData['activation_date'],
                    $rowData['expiration_date'], $rowData['auth_source'], $rowData['password']
                );
            }

            fclose($handle);
        }

        return $importUsersData;
    }
}