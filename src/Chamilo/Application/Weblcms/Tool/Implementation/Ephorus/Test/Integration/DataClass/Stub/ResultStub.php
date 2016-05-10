<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Integration\Chamilo\DataClass\Stub;

/**
 * This class extends the result dataclass
 * 
 * @package application\weblcms\tool\ephorus;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultStub extends Result
{

    /**
     * Checks if the object is valid
     * 
     * @return boolean
     */
    protected function check_before_save()
    {
        return true;
    }
}
