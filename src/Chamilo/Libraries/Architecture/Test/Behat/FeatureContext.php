<?php
namespace Chamilo\Libraries\Architecture\Test\Behat;

use Behat\MinkExtension\Context\MinkContext;
use Chamilo\Core\Install\Observer\Type\CommandLineInstaller;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\File\SystemPathBuilder;
use Exception;

/**
 * Extension on the mink context to define our own feature context for behat
 */
class FeatureContext extends MinkContext
{
    /**
     *  'I go to application "Chamilo\Core\Repository" ...'
     *  If $shouldUseLcms4Urls is true: we use the URL /index.php?application=repository
     *  Else: we generate the URL by calling Chamilo\Libraries\File\Redirect
     *  See iGoToApplication(...) and iAmInApplicationAndDoAction(...).
     */
    protected $shouldUseLcms4Urls = false;

    /**
     * Go to an application and do an action
     * @When /^I (?:am in|go to) application "(?P<application>[^"]+)" and do action "(?P<action>[^"]+)"$/
     *
     * @param unknown $application
     */
    public function iAmInApplicationAndDoAction($application, $action)
    {
        if (!$this->shouldUseLcms4Urls)
        {
            $redirect = new Redirect(
                [Application::PARAM_CONTEXT => $application, Application::PARAM_ACTION => $action]
            );

            $this->visit($redirect->getUrl());
        }
        else
        {   // Example: from 'Chamilo\Core\Repository' extract 'repository'.
            $application = strtolower(array_pop(explode('\\', $application)));
            $this->visit('/index.php?application=' . $application . '&go=' . $action);
        }

        $this->thePageShouldBeSuccessfullyLoaded();
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
     * @When /^I follow "([^"]*)" in the row containing "([^"]*)"$/
     */
    public function iFollowInTheRowContaining($linkName, $rowText)
    {
        /** @var $row \Behat\Mink\Element\NodeElement */
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        if (!$row)
        {
            throw new Exception(sprintf('Cannot find any row on the page containing the text "%s"', $rowText));
        }

        $row->clickLink($linkName);
    }

    /**
     * Go to an application
     * @When /^I (?:am in|go to) application "(?P<application>[^"]+)"$/
     *
     * @param unknown $application
     */
    public function iGoToApplication($application)
    {
        if (!$this->shouldUseLcms4Urls)
        {
            $redirect = new Redirect([Application::PARAM_CONTEXT => $application]);
            $this->visit($redirect->getUrl());
        }
        else
        {   // Example: from 'Chamilo\Core\Repository' extract 'repository'.
            $application = strtolower(array_pop(explode('\\', $application)));
            $this->visit('/index.php?application=' . $application);
        }

        $this->thePageShouldBeSuccessfullyLoaded();
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
     * Checks if no exception is shown on the page
     * @Then /^I should not see an exception$/
     */
    public function iShouldNotSeeAnExceptionBox()
    {
        $this->assertElementNotOnPage('.error-message');
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
     * Checks if an exception is shown on the page
     * @Then /^I should see an exception$/
     */
    public function iShouldSeeAnException()
    {
        $this->assertElementOnPage('.error-message');
    }

    /**
     * @BeforeSuite
     */
    public static function installChamilo()
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $config_file = $systemPathBuilder->getStoragePath() . 'configuration/command_line_configuration.php';
        $installer = new CommandLineInstaller($config_file);
        $installer->run();
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
}
