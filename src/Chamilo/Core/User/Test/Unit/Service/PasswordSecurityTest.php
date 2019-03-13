<?php

namespace Chamilo\Core\User\Test\Unit\Service;

use Chamilo\Core\User\Service\PasswordSecurity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the PasswordSecurity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PasswordSecurityTest extends ChamiloTestCase
{
    /**
     * @var PasswordSecurity
     */
    protected $passwordSecurity;

    /**
     * @var UserRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->userRepositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->passwordSecurity = new PasswordSecurity($this->userRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->userRepositoryMock);
        unset($this->passwordSecurity);
    }

    public function testSetPasswordForUser()
    {
        $user = new User();
        $password = 'testPassword';

        $this->passwordSecurity->setPasswordForUser($user, $password);
        $this->assertContains('$2y$10$', $user->get_password());
    }

    public function testIsPasswordValidForUser()
    {
        $user = new User();
        $user->set_password('$2y$10$/Epsvss4wNsAgq1BdEEUzu/LZPXdp4QSlsCNdrGlbinS272t3j.p.');
        $password = 'testPassword';

        $this->assertTrue($this->passwordSecurity->isPasswordValidForUser($user, $password));
    }

    public function testConvertPasswordForUser()
    {
        $user = new User();
        $password = 'testPassword';

        $this->userRepositoryMock->expects($this->once())
            ->method('update')
            ->with($user)
            ->will($this->returnValue(true));

        $user->set_password('oldPassword');

        $this->passwordSecurity->convertPasswordForUser($user, $password);
        $this->assertContains('$2y$10$', $user->get_password());
    }

    public function testConvertPasswordForUserWhenBCrypt()
    {
        $user = new User();
        $user->set_password('$2y$10$/Epsvss4wNsAgq1BdEEUzu/LZPXdp4QSlsCNdrGlbinS272t3j.p.');

        $this->userRepositoryMock->expects($this->never())
            ->method('update');

        $this->passwordSecurity->convertPasswordForUser($user, 'testPassword');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConvertPasswordWhenStorageFails()
    {
        $user = new User();
        $password = 'testPassword';

        $this->userRepositoryMock->expects($this->once())
            ->method('update')
            ->with($user)
            ->will($this->returnValue(false));

        $user->set_password('oldPassword');

        $this->passwordSecurity->convertPasswordForUser($user, $password);
    }
}

