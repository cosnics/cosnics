<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class IndexVisibilityChangerComponent extends Manager
{
    /**
     * Runs this component
     */
    public function run()
    {
        $this->validateAccess();

        $requests = $this->getEphorusRequestsFromRequest();
        $requestManager = $this->getRequestManager();
        $failures = $requestManager->changeDocumentsVisibility($requests);

        $message = $this->get_result(
            $failures, count($requests), 'VisibilityNotChanged', 'VisibilityNotChanged', 'VisibilityChanged',
            'VisibilityChanged'
        );

        $parameters = array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE);
        $this->redirect($message, $failures > 0, $parameters);
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_REQUEST_IDS;

        return parent::get_additional_parameters($additionalParameters);
    }

}
