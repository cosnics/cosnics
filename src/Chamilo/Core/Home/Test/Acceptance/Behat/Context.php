<?php
namespace Chamilo\Core\Home\Test\Acceptance\Behat;

use Behat\MinkExtension\Context\RawMinkContext;
use Chamilo\Libraries\Architecture\Test\Behat\FeatureContextTrait;

class Context extends RawMinkContext
{
    
    use FeatureContextTrait;

    /**
     * @Then /^I should see block "(?P<title>[^"]+)"$/
     * 
     * @param string $title The block title
     */
    public function iShouldSeeBlock($title)
    {
        $this->featureContext->assertElementContainsText('.portal-block .title', $title);
    }
}
