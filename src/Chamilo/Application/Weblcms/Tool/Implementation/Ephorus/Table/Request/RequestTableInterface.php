<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request;

/**
 * This interface describes the required methods for the request table
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface RequestTableInterface
{

    public function get_ephorus_request_url($entryId);
}
