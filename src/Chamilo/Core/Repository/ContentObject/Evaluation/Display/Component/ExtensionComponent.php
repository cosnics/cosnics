<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager as AjaxManager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;


/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class ExtensionComponent extends Manager
{
    public function run()
    {
        $this->checkAccessRights();

        BreadcrumbTrail::getInstance()->remove(count(BreadcrumbTrail::getInstance()->getBreadcrumbs()) - 1);

        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
        $applicationConfiguration->set(self::EVALUATION_URL, $this->get_url([self::PARAM_ACTION => null]));
        $applicationConfiguration->set(self::IMPORT_RESULTS_URL, $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_AJAX,
                AjaxManager::PARAM_ACTION => AjaxManager::ACTION_IMPORT
            ]
        ));

        return $this->getApplicationFactory()->getApplication(
            \Hogent\Extension\Chamilo\Core\Repository\ContentObject\Evaluation\Extension\Ans\Manager::context(),
            $applicationConfiguration
        )->run();
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation())
        {
            throw new NotAllowedException();
        }
    }
}