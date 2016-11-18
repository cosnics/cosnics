<?php
namespace Chamilo\Libraries\Architecture\Test\Behat;

use Behat\MinkExtension\Context\MinkContext;
use Chamilo\Core\Install\Observer\Type\CommandLineInstaller;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;

/**
 * Extension on the mink context to define our own feature context for behat
 */
class FeatureContext extends MinkContext
{

    /**
     * @BeforeSuite
     */
    public static function installChamilo()
    {
        $config_file = Path::getInstance()->getStoragePath() . 'configuration/command_line_configuration.php';
        $installer = new CommandLineInstaller($config_file);
        $installer->run();
    }

    /**
     * logs the user in as an admin user
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        $this->visit('/index.php');
        $this->fillField('login', 'admin');
        $this->fillField('password', 'admin');
        $this->pressButton('Login');
    }

    /**
     * Checks if a notification div is available
     * @Then /^I should see a success box$/
     */
    public function iShouldSeeASuccessBox()
    {
        $this->assertElementOnPage('.notification-4');
    }

    /**
     * Checks if a notification div is available
     * @Then /^I should see an error box$/
     */
    public function iShouldSeeAnErrorBox()
    {
        $this->assertElementOnPage('.notification-1');
    }

    /**
     * Checks if a notification div is not available
     * @Then /^I should not see an error box$/
     */
    public function iShouldNotSeeAnErrorBox()
    {
        $this->assertElementNotOnPage('.notification-1');
    }

    /**
     * Checks if an exception is shown on the page
     * @Then /^I should see an exception$/
     */
    public function iShouldSeeAnException()
    {
        $this->assertElementOnPage('.error-message');
    }

    /**
     * Checks if no exception is shown on the page
     * @Then /^I should not see an exception$/
     */
    public function iShouldNotSeeAnExceptionBox()
    {
        $this->assertElementNotOnPage('.error-message');
    }

    /**
     * Checks if the page is successfully loaded
     * @Then /^the page should be successfully loaded$/
     */
    public function thePageShouldBeSuccessfullyLoaded()
    {
        $this->assertResponseStatus(200);
        $this->iShouldNotSeeAnExceptionBox();
        $this->assertElementOnPage('#footer');
        $this->assertElementNotOnPage('table.xdebug-error');
    }

    /**
     * Go to an application
     * @When /^I (?:am in|go to) application "(?P<application>[^"]+)"$/
     * 
     * @param unknown $application
     */
    public function iGoToApplication($application)
    {
        $redirect = new Redirect(array(Application::PARAM_CONTEXT => $application));
        
        $this->visit($redirect->getUrl());
        $this->thePageShouldBeSuccessfullyLoaded();
    }

    /**
     * Go to an application and do an action
     * @When /^I (?:am in|go to) application "(?P<application>[^"]+)" and do action "(?P<action>[^"]+)"$/
     * 
     * @param unknown $application
     */
    public function iAmInApplicationAndDoAction($application, $action)
    {
        $redirect = new Redirect(
            array(Application::PARAM_CONTEXT => $application, Application::PARAM_ACTION => $action));
        
        $this->visit($redirect->getUrl());
        $this->thePageShouldBeSuccessfullyLoaded();
    }
}
