<?php
namespace Chamilo\Core\Repository\Share\Table\User;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * Data provider for a repository browser table.
 * This class implements some functions to allow repository browser tables
 * to retrieve information about the objects to display.
 */
class UserRightsTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $content_object_id = $this->get_component()->get_content_object_ids();
        if (is_array($content_object_id)) // culmination of rights cant be shown in a table browser per content object
                                          // as a user can have different rights on them
        {
            $content_object_id = $content_object_id[0];
        }
        $user_id = Session :: get_user_id();
        return \Chamilo\Core\Repository\Storage\DataManager :: get_content_object_user_shares(
            $content_object_id, 
            $user_id);
    }

    public function count_data($condition)
    {
        $content_object_id = $this->get_component()->get_content_object_ids();
        if (is_array($content_object_id)) // culmination of rights cant be shown in a table browser per content object
                                          // as a user can have different rights on them
        {
            $content_object_id = $content_object_id[0];
        }
        $user_id = Session :: get_user_id();
        return \Chamilo\Core\Repository\Storage\DataManager :: count_content_object_user_shares(
            $content_object_id, 
            $user_id);
    }
}
