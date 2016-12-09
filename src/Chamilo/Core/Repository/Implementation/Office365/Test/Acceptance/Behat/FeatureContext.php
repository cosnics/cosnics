<?php
namespace Chamilo\Core\Repository\Implementation\Office365\Test\Acceptance\Behat;

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
     * @When I fill in configured client id and secret
     */
    public function iFillInConfiguredClientIdAndSecret()
    {
        $this->fillField('settings[client_id]', $this->customParameters['client_id']);
        $this->fillField('settings[client_secret]', $this->customParameters['client_secret']);
    }

    /**
     * @When I fill in configured user id and password
     */
    public function iFillInConfiguredUserIdAndPassword()
    {
        $this->fillField('cred-userid-inputtext', $this->customParameters['user_id']);
        $this->fillField('cred-password-inputtext', $this->customParameters['password']);
    }
}
