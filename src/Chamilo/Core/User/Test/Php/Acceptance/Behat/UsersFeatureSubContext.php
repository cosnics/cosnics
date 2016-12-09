<?php
namespace Chamilo\Core\User\Test\Php\Acceptance\Behat;

use Behat\MinkExtension\Context\MinkContext;

class UsersFeatureSubContext extends MinkContext
{

    /**
     * logs the user in as an admin user
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        return $this->iAmLoggedInAs('admin');
    }

    /**
     * logs in as the given user
     * @Given /^I am logged in as "(admin|teacher|student)"$/
     */
    public function iAmLoggedInAs($user)
    {
        $this->visit('/index.php');
        $this->fillField('login', 'admin');
        $this->fillField('password', 'admin');
        $this->pressButton('Login');
    }
}