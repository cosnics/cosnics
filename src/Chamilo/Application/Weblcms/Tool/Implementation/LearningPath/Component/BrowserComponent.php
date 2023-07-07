<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
