<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Integration\Chamilo\DataClass\Stub;

/**
 * This class extends the request dataclass
 * 
 * @package application\weblcms\tool\ephorus;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestStub extends Request
{

    /**
     * Checks if the object is valid
     * 
     * @return boolean
     */
    protected function checkBeforeSave()
    {
        return true;
    }
}
