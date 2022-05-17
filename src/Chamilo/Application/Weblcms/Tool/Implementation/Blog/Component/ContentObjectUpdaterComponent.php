<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Blog\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Blog\Manager;

class ContentObjectUpdaterComponent extends Manager
{

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
