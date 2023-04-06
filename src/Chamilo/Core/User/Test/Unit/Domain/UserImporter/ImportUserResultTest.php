<?php

namespace Chamilo\Core\User\Test\Unit\Domain\UserImporter;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Domain\UserImporter\ImportUserResult;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the ImportUserResult class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserResultTest extends ChamiloTestCase
{
    /**
     * @var ImportUserResult
     */
    protected $importUserResult;

    protected function setUp(): void    {
        $importData = new ImportUserData('test');
        $this->importUserResult = new ImportUserResult($importData);
    }

    protected function tearDown(): void    {
        unset($this->importUserResult);
    }

    public function testSetGetStatus()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_SUCCESS);
        $this->assertEquals(ImportUserResult::STATUS_SUCCESS, $this->importUserResult->getStatus());
    }

    public function testSetGetMessages()
    {
        $messages = ['test'];
        $this->importUserResult->setMessages($messages);
        $this->assertEquals($messages, $this->importUserResult->getMessages());
    }

    public function testSetGetImportUserData()
    {
        $importData = new ImportUserData('test2');
        $this->importUserResult->setImportUserData($importData);
        $this->assertEquals($importData, $this->importUserResult->getImportUserData());
    }

    public function testAddMessage()
    {
        $messages = ['test'];
        $this->importUserResult->addMessage('test');
        $this->assertEquals($messages, $this->importUserResult->getMessages());
    }

    public function testSetFailed()
    {
        $this->importUserResult->setFailed();
        $this->assertEquals(ImportUserResult::STATUS_FAILED, $this->importUserResult->getStatus());
    }

    public function testSetSuccessful()
    {
        $this->importUserResult->setSuccessful();
        $this->assertEquals(ImportUserResult::STATUS_SUCCESS, $this->importUserResult->getStatus());
    }

    public function testHasFailed()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_FAILED);
        $this->assertTrue($this->importUserResult->hasFailed());
    }

    public function testHasFailedWhenSuccess()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_SUCCESS);
        $this->assertFalse($this->importUserResult->hasFailed());
    }

    public function testIsSuccessful()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_SUCCESS);
        $this->assertTrue($this->importUserResult->isSuccessful());
    }

    public function testIsSuccessfulWhenFailed()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_FAILED);
        $this->assertFalse($this->importUserResult->isSuccessful());
    }

    public function testIsCompleted()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_SUCCESS);
        $this->assertTrue($this->importUserResult->isCompleted());
    }

    public function testIsCompletedWithStatusFailed()
    {
        $this->importUserResult->setStatus(ImportUserResult::STATUS_FAILED);
        $this->assertTrue($this->importUserResult->isCompleted());
    }

    public function testIsCompletedWithNoStatus()
    {
        $this->assertFalse($this->importUserResult->isCompleted());
    }


}