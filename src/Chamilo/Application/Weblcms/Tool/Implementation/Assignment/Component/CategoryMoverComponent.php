<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;

/**
 * CategoryMover component for the assignment tool.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CategoryMoverComponent extends Manager
{

    /**
     * #Override Returns the additional parameters as an array.
     * 
     * @return array The additional parameters
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
