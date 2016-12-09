<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video\Test\Acceptance\Behat;

class FeatureContext extends \Chamilo\Libraries\Architecture\Test\Behat\FeatureContext
{    
    private $customParameters;
    
    public function __construct($customParameters)
    {
        $this->customParameters = $customParameters;

        // See \Chamilo\Libraries\Architecture\Test\Behat\FeatureContext :: shouldUseLcms4Urls
        $this->shouldUseLcms4Urls = boolval($this->customParameters['use_lcms4_urls']);
    }

    /**
     * @When I fill in configured client id, secret, and sharepoint URL
     */
    public function iFillInConfiguredClientIdSecretAndSharepointUrl()
    {
        $this->fillField('settings[client_id]', $this->customParameters['client_id']);
        $this->fillField('settings[client_secret]', $this->customParameters['client_secret']);
        $this->fillField('settings[root_site]', $this->customParameters['root_site']);
    }

    /**
     * @When I submit configured user id and password
     */
    public function iSubmitConfiguredUserIdAndPassword()
    {
        $this->fillField('cred_userid_inputtext', $this->customParameters['user_id']);
        $this->fillField('cred_password_inputtext', $this->customParameters['password']);
        
        // 'I press "Sign In"' command throws an exception saying that the 'Sign In' button is outside the form.
        // This solution submits the form without pressing the 'Sign In' button. 
        $this->getSession()->getPage()->find('xpath', 'descendant-or-self::form[1]')->submit();    
    }


    /**
     * @When /^I click on thumbnail "([^"]*)"$/
     */
    public function iClickOnThumbnail($videoTitle)
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table td:contains("%s")', $videoTitle));
        if (! $row) 
        {
            throw new \Exception(sprintf('Cannot find any table cell on the page containing the text "%s"', $videoTitle));
        }

        $image = $row->find('css', '.thumbnail');
        if (! $image)
        {
            throw new \Exception(sprintf('Cannot find image of class "thumbnail" in table cell containing the text "%s"', $videoTitle));
        }

        $image->getParent()->click();
    }}
