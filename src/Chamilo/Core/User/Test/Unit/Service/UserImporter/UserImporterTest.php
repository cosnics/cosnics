<?php

namespace Chamilo\Core\User\Test\Unit\Service\UserImporter;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Service\PasswordSecurity;
use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserFactory;
use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserInterface;
use Chamilo\Core\User\Service\UserImporter\UserImporter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\Translator;

/**
 * Tests the UserImporter
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserImporterTest extends ChamiloTestCase
{
    /**
     * @var UserImporter
     */
    protected $userImporter;

    /**
     * @var ImportParserFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $importParserFactoryMock;

    /**
     * @var UserRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRepositoryMock;

    /**
     * @var ConfigurationConsulter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var PasswordSecurity | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $passwordSecurityMock;

    /**
     * @var MailerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mailerMock;

    /**
     * @var Translator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;

    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->importParserFactoryMock = $this->getMockBuilder(ImportParserFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->userRepositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->configurationConsulterMock = $this->getMockBuilder(ConfigurationConsulter::class)
            ->disableOriginalConstructor()->getMock();

        $this->passwordSecurityMock = $this->getMockBuilder(PasswordSecurity::class)
            ->disableOriginalConstructor()->getMock();

        $this->mailerMock = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()->getMock();

        $this->userImporter = new UserImporter(
            $this->importParserFactoryMock, $this->userRepositoryMock, $this->configurationConsulterMock,
            $this->passwordSecurityMock, $this->mailerMock, $this->translatorMock
        );

        $this->translatorMock->expects($this->any())
            ->method('trans')
            ->will($this->returnArgument(0));

        $this->uploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->userImporter);
    }

    public function testImportUsersFromFile()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate();
        $this->mockCreateUserSettingForSettingAndUser();

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertEquals(1, $userImporterResult->countSuccessUserResults());
    }

    public function testImportUsersFromFileWillTriggerImportEvent()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate();

        $this->userRepositoryMock->expects($this->once())
            ->method('triggerImportEvent');

        $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
    }

    public function testImportUsersFromFileWillSendMail()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate();

        $this->mailerMock->expects($this->once())
            ->method('sendMail');

        $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile, true);
    }

    public function testImportUsersFromFileWillSetMessageOnMailerException()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate();
        $this->mockCreateUserSettingForSettingAndUser();

        $this->mailerMock->expects($this->once())
            ->method('sendMail')
            ->will($this->throwException(new \Exception()));

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile, true);
        $this->assertTrue(
            in_array('ImportUserMailNotSent', $userImporterResult->getSuccessUserResults()[0]->getMessages())
        );
    }

    /**
     * @throws \Exception
     */
    public function testImportUsersWithInvalidUsername()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', '', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(0);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array('ImportUserNoUsernameFound', $userImporterResult->getFailedUserResults()[0]->getMessages())
        );
    }

    /**
     * @throws \Exception
     */
    public function testImportUsersWithInvalidAction()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'C', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(0);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array('ImportUserNoValidActionFound', $userImporterResult->getFailedUserResults()[0]->getMessages())
        );
    }

    /**
     * @throws \Exception
     */
    public function testImportUsersWithInvalidActive()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', null,
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(1);
        $this->mockCreateUserSettingForSettingAndUser();

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);

        $this->assertTrue(
            in_array('ImportUserActiveNotFound', $userImporterResult->getSuccessUserResults()[0]->getMessages())
        );

        $this->assertTrue(
            $userImporterResult->getSuccessUserResults()[0]->getImportUserData()->getUser()->get_active()
        );
    }

    public function testImportUsersWithInvalidAuthSource()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', null,
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', '', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(1);
        $this->mockCreateUserSettingForSettingAndUser();

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);

        $this->assertTrue(
            in_array('ImportUserAuthSourceNotFound', $userImporterResult->getSuccessUserResults()[0]->getMessages())
        );

        $this->assertEquals(
            'Platform',
            $userImporterResult->getSuccessUserResults()[0]->getImportUserData()->getUser()->getAuthenticationSource()
        );
    }

    public function testImportUsersWithInvalidStatus()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '2', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', '', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(1);
        $this->mockCreateUserSettingForSettingAndUser();

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);

        $this->assertTrue(
            in_array('ImportUserNoValidStatusFound', $userImporterResult->getSuccessUserResults()[0]->getMessages())
        );

        $this->assertEquals(
            User::STATUS_STUDENT,
            $userImporterResult->getSuccessUserResults()[0]->getImportUserData()->getUser()->get_status()
        );
    }

    public function testImportUsersWithInvalidLanguage()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'de', '2', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', '', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(1);
        $this->mockCreateUserSettingForSettingAndUser();

        $this->configurationConsulterMock->expects($this->at(1))
            ->method('getSetting')
            ->with(['Chamilo\Core\Admin', 'platform_language'])
            ->will($this->returnValue('en'));

        $this->userRepositoryMock->expects($this->once())
            ->method('createUserSettingForSettingAndUser')
            ->with('Chamilo\Core\Admin', 'platform_language', $this->anything(), 'en');

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);

        $this->assertTrue(
            in_array('ImportUserNoValidLanguageFound', $userImporterResult->getSuccessUserResults()[0]->getMessages())
        );
    }

    public function testImportUsersWithInvalidEmail()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', '', '123456789', 'de', '2', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', '', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);

        $this->configurationConsulterMock->expects($this->at(0))
            ->method('getSetting')
            ->with(['Chamilo\Core\User', 'require_email'])
            ->will($this->returnValue(true));

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);

        $this->assertTrue(
            in_array(
                'ImportUserEmailRequiredForNewUsers', $userImporterResult->getFailedUserResults()[0]->getMessages()
            )
        );
    }

    public function testImportUsersWithCreateFailed()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockUserCreate(1, false);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array('ImportUserCouldNotCreateUserInDatabase', $userImporterResult->getFailedUserResults()[0]->getMessages())
        );
    }

    public function testImportUsersWithCreateWhenUserExists()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'A', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array('ImportUserUserAlreadyExists', $userImporterResult->getFailedUserResults()[0]->getMessages())
        );
    }

    public function testImportUsersWithUpdate()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'A;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'U', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());
        $this->mockCreateUserSettingForSettingAndUser();
        $this->mockUserUpdate(1, true);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertEquals(1, $userImporterResult->countSuccessUserResults());
    }

    public function testImportUsersWithUpdateFailed()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'U;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'U', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());
        $this->mockUserUpdate(1, false);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array(
                'ImportUserCouldNotUpdateUserInDatabase', $userImporterResult->getFailedUserResults()[0]->getMessages()
            )
        );
    }

    public function testImportUsersWithUpdateUserNotExists()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'U;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'U', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array(
                'ImportUserUserDoesNotExist', $userImporterResult->getFailedUserResults()[0]->getMessages()
            )
        );
    }

    public function testImportUsersWithUpdatEmptyLanguage()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'U;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'U', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', '', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());
        $this->mockUserUpdate(1, true);

        $this->userRepositoryMock->expects($this->never())
            ->method('createUserSettingForSettingAndUser');

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertEquals(1, $userImporterResult->countSuccessUserResults());
    }

    public function testImportUsersWithDelete()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'D;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'D', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());
        $this->mockUserUpdate(1, true);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertEquals(1, $userImporterResult->countSuccessUserResults());
    }

    public function testImportUsersWithDeleteFailed()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'D;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'D', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());
        $this->mockUserUpdate(1, false);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array(
                'ImportUserCouldNotDeleteUserFromDatabase',
                $userImporterResult->getFailedUserResults()[0]->getMessages()
            )
        );
    }

    public function testImportUsersWithDeleteUserNotExists()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'D;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'D', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertTrue(
            in_array(
                'ImportUserUserAlreadyRemoved', $userImporterResult->getSuccessUserResults()[0]->getMessages()
            )
        );
    }

    public function testImportUsersWithUpdateAddWhenUserExists()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'U;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'UA', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockCreateUserSettingForSettingAndUser();
        $this->mockFindUserByUsername(new User());
        $this->mockUserUpdate(1, true);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertEquals(1, $userImporterResult->countSuccessUserResults());
    }

    public function testImportUsersWithUpdateAddWhenUserNotExists()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'U;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'UA', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockCreateUserSettingForSettingAndUser();
        $this->mockUserCreate(1, true);

        $userImporterResult = $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile);
        $this->assertEquals(1, $userImporterResult->countSuccessUserResults());
    }

    public function testImportUsersFromFileWithUpdateWillSendMail()
    {
        $importUsersData = [];

        $importUsersData[] = new ImportUserData(
            'U;test001;Eric;Peeters;no-reply@test.com;123456789;nl;5;1;4487965131387;2017-01-05 00:00:00;2017-01-05 00:00:00;Platform;blablabla',
            'U', 'test001', 'Eric', 'Peeters', 'no-reply@test.com', '123456789', 'nl', '5', '1',
            '4487965131387', '2017-01-05 00:00:00', '2017-01-05 00:00:00', 'Platform', 'blablabla'
        );

        $this->mockImportUserData($importUsersData);
        $this->mockFindUserByUsername(new User());
        $this->mockUserUpdate(1);

        $this->mailerMock->expects($this->once())
            ->method('sendMail');

        $this->userImporter->importUsersFromFile(new User(), $this->uploadedFile, true);
    }

    /**
     * Helper function to mock the import user data
     *
     * @param ImportUserData[] $importUsersData
     */
    protected function mockImportUserData($importUsersData = [])
    {
        $importParserMock = $this->getMockBuilder(ImportParserInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->importParserFactoryMock->expects($this->once())
            ->method('getImportParserForUploadedFile')
            ->with($this->uploadedFile)
            ->will($this->returnValue($importParserMock));

        $importParserMock->expects($this->once())
            ->method('parse')
            ->with($this->uploadedFile)
            ->will($this->returnValue($importUsersData));
    }

    /**
     * Mocks the create function from the UserRepository
     *
     * @param int $numberOfCalls
     * @param bool $returnValue
     */
    protected function mockUserCreate($numberOfCalls = 1, $returnValue = true)
    {
        $this->userRepositoryMock->expects($this->exactly($numberOfCalls))
            ->method('create')
            ->will($this->returnValue($returnValue));
    }

    /**
     * Mocks the update function from the UserRepository
     *
     * @param int $numberOfCalls
     * @param bool $returnValue
     */
    protected function mockUserUpdate($numberOfCalls = 1, $returnValue = true)
    {
        $this->userRepositoryMock->expects($this->exactly($numberOfCalls))
            ->method('update')
            ->will($this->returnValue($returnValue));
    }

    /**
     * Mocks the createUserSettingForSettingAndUser function from the UserRepository
     */
    protected function mockCreateUserSettingForSettingAndUser()
    {
        $this->userRepositoryMock->expects($this->once())
            ->method('createUserSettingForSettingAndUser')
            ->will($this->returnValue(true));
    }

    /**
     * Mocks the create function from the UserRepository
     *
     * @param User|null $returnUser
     */
    protected function mockFindUserByUsername(User $returnUser = null)
    {
        $this->userRepositoryMock->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($returnUser));
    }

}

