<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Integration\Chamilo\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\RequestStub;
use Chamilo\Libraries\Storage\DataClassCRUDTest;

/**
 * Tests the integration of the request data_class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestIntegrationTest extends DataClassCRUDTest
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns an instance of the dataclass extension
     * 
     * @return DataClass
     */
    public function get_object()
    {
        return new RequestStub();
    }
}
