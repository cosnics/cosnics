<?php
namespace Chamilo\Core\User\Test\Domain\UserImporter;

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
    public function setUp()
    {
        $this->userImporterResult = new UserImporterResult();
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
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
        $this->userImporterResult->addFailedUserResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getFailedUserResults());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddFailedUserResultWhenNotFailed()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $this->userImporterResult->addFailedUserResult($importUserResult);
    }

    public function testAddSuccessUserResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addSuccessUserResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getSuccessUserResults());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddSuccessUserResultWhenNotSuccess()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $this->userImporterResult->addSuccessUserResult($importUserResult);
    }

    public function testAddImportUserResultWithSuccessfulResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addImportUserResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getSuccessUserResults());
    }

    public function testAddImportUserResultWithFailedResult()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addImportUserResult($importUserResult);
        $this->assertCount(1, $this->userImporterResult->getFailedUserResults());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddImportUserResultIncomplete()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $this->userImporterResult->addImportUserResult($importUserResult);
    }

    public function testCountSuccessUserResults()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addImportUserResult($importUserResult);
        $this->assertEquals(1, $this->userImporterResult->countSuccessUserResults());
    }

    public function testCountFailedUserResults()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addImportUserResult($importUserResult);
        $this->assertEquals(1, $this->userImporterResult->countFailedUserResults());
    }

    public function testCountResults()
    {
        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setSuccessful();
        $this->userImporterResult->addImportUserResult($importUserResult);

        $importUserResult = new ImportUserResult(new ImportUserData('test'));
        $importUserResult->setFailed();
        $this->userImporterResult->addImportUserResult($importUserResult);

        $this->assertEquals(2, $this->userImporterResult->countResults());
    }
}

