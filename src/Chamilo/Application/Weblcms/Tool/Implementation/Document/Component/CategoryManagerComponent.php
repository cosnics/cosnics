<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

class CategoryManagerComponent extends Manager implements BreadcrumbLessComponentInterface
{

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
