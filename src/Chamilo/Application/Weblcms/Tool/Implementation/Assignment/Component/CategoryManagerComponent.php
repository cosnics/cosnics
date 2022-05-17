<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * CategoryManager component for the assignment tool.
 *
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CategoryManagerComponent extends Manager implements DelegateComponent
{

    /**
     * #Override Returns additional parameters as an array.
     *
     * @return array The additional parameters
     */
    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID;

        return parent::get_additional_parameters($additionalParameters);
    }
}
