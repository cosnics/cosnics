<?php
namespace Chamilo\Core\User\Test\Unit\Domain\UserImporter;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Domain\UserImporter\ImportUserResult;
use Chamilo\Core\User\Domain\UserImporter\UserImporterResult;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the UserImporterResult
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserImporterResultTest extends ChamiloTestCase
{
    /**
     * @var UserImporterResult
     */
    protected $userImporterResult;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->userImporterResult = new UserImporterResult();
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->userImporterResult);
    }

    public function testSetGetRawImportDataHeader()
    {
        $rawHeader = 'test;test2';
        $this->userImporterResult->setRawImportDataHeader($rawHeader);
        $this->assertEquals($rawHeader, $this->userImporterResult->getRawImportDataHeader());
    }

    public function testSetGetRawImportDataFooter()
    {
        $rawHeader = 'test;test2';
        $this->userImporterResult->setRawImportDataFooter($rawHeader);
        $this->assertEquals($rawHeader, $this->userImporterResult->getRawImportDataFooter());
    }

    public function testAddFailedUserResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addFailedImportDataResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getFailedUserResults());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddFailedUserResultWhenNotFailed()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $this->userImporterResult->addFailedImportDataResult($importUserResult);
    }

    public function testAddSuccessUserResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addSuccessImportDataResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getSuccessUserResults());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddSuccessUserResultWhenNotSuccess()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $this->userImporterResult->addSuccessImportDataResult($importUserResult);
    }

    public function testAddImportUserResultWithSuccessfulResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addImportDataResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getSuccessUserResults());
    }

    public function testAddImportUserResultWithFailedResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addImportDataResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getFailedUserResults());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddImportUserResultIncomplete()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $this->userImporterResult->addImportDataResult($importUserResult);
    }

    public function testCountSuccessUserResults()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addImportDataResult($importUserResult);
        $this->assertEquals(1, $this->userImporterResult->countSuccessUserResults());
    }

    public function testCountFailedUserResults()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addImportDataResult($importUserResult);
        $this->assertEquals(1, $this->userImporterResult->countFailedUserResults());
    }

    public function testCountResults()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addImportDataResult($importUserResult);

        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addImportDataResult($importUserResult);

        $this->assertEquals(2, $this->userImporterResult->countResults());
    }
}

