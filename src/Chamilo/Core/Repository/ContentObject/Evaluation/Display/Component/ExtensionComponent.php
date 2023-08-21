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

        return $this->getApplicationFactory()->getApplication(
            self::EXTENSION_NAMESPACE,
            $this->getExtensionApplicationConfiguration()
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