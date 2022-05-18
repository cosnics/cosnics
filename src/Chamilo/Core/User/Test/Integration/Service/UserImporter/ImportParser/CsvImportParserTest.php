<?php
namespace Chamilo\Core\User\Test\Integration\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Domain\UserImporter\UserImporterResult;
use Chamilo\Core\User\Service\UserImporter\ImportParser\CsvImportParser;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Tests the CsvImportParser
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CsvImportParserTest extends ChamiloTestCase
{

    /**
     *
     * @var CsvImportParser
     */
    protected $csvImportParser;

    /**
     *
     * @var UserImporterResult
     */
    protected $userImporterResult;

    /**
     *
     * @var ImportUserData[]
     */
    protected $importedUserData;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->csvImportParser = new CsvImportParser(StringUtilities::getInstance());

        $this->userImporterResult = new UserImporterResult();

        $this->importedUserData = $this->csvImportParser->parse(
            new UploadedFile(__DIR__ . '/test.csv', 'test.csv', 'text/csv'),
            $this->userImporterResult);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->importedUserData);
        unset($this->userImporterResult);
        unset($this->csvImportParser);
    }

    public function testCanParseFile()
    {
        $this->assertTrue(
            $this->csvImportParser->canParseFile(new UploadedFile(__DIR__ . '/test.csv', 'test.csv', 'text/csv')));
    }

    public function testParseSetsImportDataHeader()
    {
        $this->assertEquals(
            'action;username;firstname;lastname;email;official_code;language;status;active;phone;' .
                 'activation_date;expiration_date;auth_source;password',
                $this->userImporterResult->getRawImportDataHeader());
    }

    public function testParseSetsRawImportData()
    {
        $this->assertEquals(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;' .
                 '2017-01-05 00:00:00;Platform;blablabla',
                $this->importedUserData[0]->getRawImportData());
    }

    public function testParseSetsAction()
    {
        $this->assertEquals('A', $this->importedUserData[0]->getAction());
    }

    public function testParseSetsUsername()
    {
        $this->assertEquals('test001', $this->importedUserData[0]->getUsername());
    }

    public function testParseSetsFirstName()
    {
        $this->assertEquals('Eric', $this->importedUserData[0]->getFirstName());
    }

    public function testParseSetsLastName()
    {
        $this->assertEquals('Peeters', $this->importedUserData[0]->getLastName());
    }

    public function testParseSetsEmail()
    {
        $this->assertEquals('no-reply@test.com', $this->importedUserData[0]->getEmail());
    }

    public function testParseSetsOfficialCode()
    {
        $this->assertEquals('123456789', $this->importedUserData[0]->getOfficialCode());
    }

    public function testParseSetsLanguage()
    {
        $this->assertEquals('nl', $this->importedUserData[0]->getLanguage());
    }

    public function testParseSetsStatus()
    {
        $this->assertEquals('5', $this->importedUserData[0]->getStatus());
    }

    public function testParseSetsActive()
    {
        $this->assertEquals('1', $this->importedUserData[0]->getActive());
    }

    public function testParseSetsPhone()
    {
        $this->assertEquals('4487965131387', $this->importedUserData[0]->getPhone());
    }

    public function testParseSetsActivationDate()
    {
        $this->assertEquals('2017-01-05 00:00:00', $this->importedUserData[0]->getActivationDate());
    }

    public function testParseSetsExpirationDate()
    {
        $this->assertEquals('2017-01-05 00:00:00', $this->importedUserData[0]->getExpirationDate());
    }

    public function testParseSetsAuthSource()
    {
        $this->assertEquals('Platform', $this->importedUserData[0]->getAuthSource());
    }

    public function testParseSetsPassword()
    {
        $this->assertEquals('blablabla', $this->importedUserData[0]->getPassword());
    }
}