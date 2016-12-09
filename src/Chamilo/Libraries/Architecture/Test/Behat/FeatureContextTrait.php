<?php
namespace Chamilo\Libraries\Architecture\Test\Behat;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;

trait FeatureContextTrait
{

    private $featureContext;

    /** @BeforeScenario */
    public function loadFeatureContext(BeforeScenarioScope $scope)
    {
        $this->featureContext = $scope->getEnvironment()->getContext('Chamilo\Libraries\Architecture\Test\Behat\FeatureContext');
    }

    /**
     * @return \FeatureContext
     */
    protected function getFeatureContext()
    {
        return $this->featureContext;
    }

}
