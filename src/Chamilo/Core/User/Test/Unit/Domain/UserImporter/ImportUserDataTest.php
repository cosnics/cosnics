<?php

namespace Chamilo\Core\User\Test\Unit\Domain\UserImporter;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Domain\UserImporter\ImportUserResult;
use Chamilo\Core\User\Service\PasswordSecurity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 * Tests the ImportUserData
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserDataTest extends ChamiloTestCase
{
    /**
     * @var ImportUserData
     */
    protected $importUserData;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->importUserData = new ImportUserData('test');
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->importUserData);
    }

    public function testSetGetRawImportData()
    {
        $rawImportData = 'test;test2';
        $this->importUserData->setRawImportData($rawImportData);
        $this->assertEquals($rawImportData, $this->importUserData->getRawImportData());
    }

    public function testSetGetAction()
    {
        $action = 'A';
        $this->importUserData->setAction($action);
        $this->assertEquals($action, $this->importUserData->getAction());
    }

    public function testSetGetUsername()
    {
        $username = 'test001';
        $this->importUserData->setUsername($username);
        $this->assertEquals($username, $this->importUserData->getUsername());
    }

    public function testSetGetFirstName()
    {
        $firstName = 'Eric';
        $this->importUserData->setFirstName($firstName);
        $this->assertEquals($firstName, $this->importUserData->getFirstName());
    }

    public function testSetGetLastName()
    {
        $lastName = 'Peeters';
        $this->importUserData->setLastName($lastName);
        $this->assertEquals($lastName, $this->importUserData->getLastName());
    }

    public function testSetGetEmail()
    {
        $email = 'no-reply@test.com';
        $this->importUserData->setEmail($email);
        $this->assertEquals($email, $this->importUserData->getEmail());
    }

    public function testSetGetOfficialCode()
    {
        $officialCode = '123456789';
        $this->importUserData->setOfficialCode($officialCode);
        $this->assertEquals($officialCode, $this->importUserData->getOfficialCode());
    }

    public function testSetGetLanguage()
    {
        $language = 'nl';
        $this->importUserData->setLanguage($language);
        $this->assertEquals($language, $this->importUserData->getLanguage());
    }

    public function testSetGetStatus()
    {
        $status = '5';
        $this->importUserData->setStatus($status);
        $this->assertEquals($status, $this->importUserData->getStatus());
    }

    public function testSetGetActive()
    {
        $active = '1';
        $this->importUserData->setActive($active);
        $this->assertEquals($active, $this->importUserData->getActive());
    }

    public function testSetGetPhone()
    {
        $phone = '4487965131387';
        $this->importUserData->setPhone($phone);
        $this->assertEquals($phone, $this->importUserData->getPhone());
    }

    public function testSetGetActivationDate()
    {
        $activationDate = '01-05-2017';
        $this->importUserData->setActivationDate($activationDate);
        $this->assertEquals($activationDate, $this->importUserData->getActivationDate());
    }

    public function testSetGetExpirationDate()
    {
        $expirationDate = '01-05-2017';
        $this->importUserData->setExpirationDate($expirationDate);
        $this->assertEquals($expirationDate, $this->importUserData->getExpirationDate());
    }

    public function testSetGetAuthSource()
    {
        $authSource = 'Platform';
        $this->importUserData->setAuthSource($authSource);
        $this->assertEquals($authSource, $this->importUserData->getAuthSource());
    }

    public function testSetGetPassword()
    {
        $password = 'blablabla';
        $this->importUserData->setPassword($password);
        $this->assertEquals($password, $this->importUserData->getPassword());
    }

    public function testSetGetUser()
    {
        $user = new User();
        $this->importUserData->setUser($user);
        $this->assertEquals($user, $this->importUserData->getUser());
    }

    public function testSetGetImportUserResult()
    {
        $importUserResult = new ImportUserResult($this->importUserData);
        $this->importUserData->setImportUserResult($importUserResult);
        $this->assertEquals($importUserResult, $this->importUserData->getImportUserResult());
    }

    public function testIsNew()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_ADD);
        $this->assertTrue($this->importUserData->isNew());
    }

    public function testIsNewWhenNotNew()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_UPDATE);
        $this->assertFalse($this->importUserData->isNew());
    }

    public function testIsNewOrUpdate()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_ADD_UPDATE);
        $this->assertTrue($this->importUserData->isNewOrUpdate());
    }

    public function testIsNewOrUpdateWhenNotNewOrUpdate()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_UPDATE);
        $this->assertFalse($this->importUserData->isNewOrUpdate());
    }

    public function testIsUpdate()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_UPDATE);
        $this->assertTrue($this->importUserData->isUpdate());
    }

    public function testIsUpdateWhenNotUpdate()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_ADD);
        $this->assertFalse($this->importUserData->isUpdate());
    }

    public function testIsDelete()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_DELETE);
        $this->assertTrue($this->importUserData->isDelete());
    }

    public function testIsDeleteWhenNotDelete()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_ADD);
        $this->assertFalse($this->importUserData->isDelete());
    }

    public function testHasValidAction()
    {
        $this->importUserData->setAction(ImportUserData::ACTION_DELETE);
        $this->assertTrue($this->importUserData->hasValidAction());
    }

    public function testHasValidActionWhenNotValid()
    {
        $this->importUserData->setAction('C');
        $this->assertFalse($this->importUserData->hasValidAction());
    }

    public function testSetActionToNew()
    {
        $this->importUserData->setActionToNew();
        $this->assertEquals(ImportUserData::ACTION_ADD, $this->importUserData->getAction());
    }

    public function testSetActionToUpdate()
    {
        $this->importUserData->setActionToUpdate();
        $this->assertEquals(ImportUserData::ACTION_UPDATE, $this->importUserData->getAction());
    }

    public function testHasValidStatus()
    {
        $this->importUserData->setStatus(User::STATUS_STUDENT);
        $this->assertTrue($this->importUserData->hasValidStatus());
    }

    public function testHasValidStatusWhenNotValid()
    {
        $this->importUserData->setAction(2);
        $this->assertFalse($this->importUserData->hasValidStatus());
    }

    public function testSetStatusToStudent()
    {
        $this->importUserData->setStatusToStudent();
        $this->assertEquals(User::STATUS_STUDENT, $this->importUserData->getStatus());
    }

    public function testIsActiveSet()
    {
        $this->importUserData->setActive(1);
        $this->assertTrue($this->importUserData->isActiveSet());
    }

    public function testIsActiveSetWhenNotSet()
    {
        $this->assertFalse($this->importUserData->isActiveSet());
    }

    public function testHasValidLanguage()
    {
        $this->importUserData->setLanguage('nl');
        $this->assertTrue($this->importUserData->hasValidLanguage());
    }

    public function testHasValidLanguageWithInvalidLanguage()
    {
        $this->importUserData->setLanguage('sk');
        $this->assertFalse($this->importUserData->hasValidLanguage());
    }

    /**
     * @throws \Exception
     */
    public function testSetPropertiesForUser()
    {
        /** @var PasswordSecurity|\PHPUnit\Framework\MockObject\MockObject $passwordSecurityMock */
        $passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $passwordSecurityMock->expects($this->once())
            ->method('setPasswordForUser')
            ->will(
                $this->returnCallback(
                    function ($user, $password) {
                        $user->set_password($password);

                        return $user;
                    }
                )
            );

        $action = 'A';
        $this->importUserData->setAction($action);

        $username = 'test001';
        $this->importUserData->setUsername($username);

        $firstName = 'Eric';
        $this->importUserData->setFirstName($firstName);

        $lastName = 'Peeters';
        $this->importUserData->setLastName($lastName);

        $email = 'no-reply@test.com';
        $this->importUserData->setEmail($email);

        $officialCode = '123456789';
        $this->importUserData->setOfficialCode($officialCode);

        $status = '5';
        $this->importUserData->setStatus($status);

        $active = '1';
        $this->importUserData->setActive($active);

        $phone = '4487965131387';
        $this->importUserData->setPhone($phone);

        $activationDate = '2017-01-05 00:00:00';
        $this->importUserData->setActivationDate($activationDate);

        $expirationDate = '2017-01-05 00:00:00';
        $this->importUserData->setExpirationDate($expirationDate);

        $authSource = 'Platform';
        $this->importUserData->setAuthSource($authSource);

        $password = 'blablabla';
        $this->importUserData->setPassword($password);

        $user = new User();
        $this->importUserData->setUser($user);

        $this->importUserData->setPropertiesForUser($passwordSecurityMock);

        $referenceDate = new \DateTime('2017-01-05 00:00:00');

        $this->assertEquals($username, $user->get_username());
        $this->assertEquals(0, $user->get_platformadmin());
        $this->assertEquals($firstName, $user->get_firstname());
        $this->assertEquals($lastName, $user->get_lastname());
        $this->assertEquals($email, $user->get_email());
        $this->assertEquals($officialCode, $user->get_official_code());
        $this->assertEquals($status, $user->get_status());
        $this->assertEquals($active, $user->get_active());
        $this->assertEquals($phone, $user->get_phone());
        $this->assertEquals($referenceDate->getTimestamp(), $user->get_activation_date());
        $this->assertEquals($referenceDate->getTimestamp(), $user->get_expiration_date());
        $this->assertEquals($authSource, $user->getAuthenticationSource());
        $this->assertEquals($password, $user->get_password());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetPropertiesForUserInvalidUser()
    {
        /** @var PasswordSecurity $passwordSecurityMock */
        $passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $this->importUserData->setPropertiesForUser($passwordSecurityMock);
    }

    public function testSetPropertiesForUserGeneratesPasswordWhenNotGiven()
    {
        $action = 'A';
        $this->importUserData->setAction($action);

        $user = new User();
        $this->importUserData->setUser($user);

        /** @var PasswordSecurity|\PHPUnit\Framework\MockObject\MockObject $passwordSecurityMock */
        $passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $passwordSecurityMock->expects($this->once())
            ->method('setPasswordForUser')
            ->will(
                $this->returnCallback(
                    function ($user, $password) {
                        $user->set_password($password);

                        return $user;
                    }
                )
            );

        $this->importUserData->setPropertiesForUser($passwordSecurityMock);

        $this->assertNotEmpty($user->get_password());
    }

    public function testSetPropertiesForUserNotNew()
    {
        /** @var PasswordSecurity $passwordSecurityMock */
        $passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $action = 'U';
        $this->importUserData->setAction($action);

        $username = 'test001';
        $this->importUserData->setUsername($username);

        $user = new User();
        $this->importUserData->setUser($user);

        $this->importUserData->setPropertiesForUser($passwordSecurityMock);

        $this->assertEmpty($user->get_password());
        $this->assertEmpty($user->get_username());
    }

    public function testSetPropertiesForUserNotifyReactivate()
    {
        /** @var PasswordSecurity $passwordSecurityMock */
        $passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();
        $user->set_active(false);

        $this->importUserData->setUser($user);
        $this->importUserData->setAction('U');
        $this->importUserData->setActive(1);

        $this->importUserData->setPropertiesForUser($passwordSecurityMock);

        $this->assertTrue($this->importUserData->mustNotifyUser());
    }

    public function testSetPropertiesForUserNotifyPasswordChange()
    {
        /** @var PasswordSecurity $passwordSecurityMock */
        $passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();
        $user->set_active(true);

        $this->importUserData->setUser($user);
        $this->importUserData->setAction('U');
        $this->importUserData->setActive(1);
        $this->importUserData->setPassword('blablabla');

        $this->importUserData->setPropertiesForUser($passwordSecurityMock);

        $this->assertTrue($this->importUserData->mustNotifyUser());
    }
}

