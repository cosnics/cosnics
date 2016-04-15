<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Helper object to prevent constructing a complete user object from the database when only an id is used in the end;
 * use with care.
 */
class UserIDWrapper extends User
{

    private $wrapper_id;

    public function __construct($id)
    {
        // no need for calling the parent constructor
        // parent :: __construct();
        $this->wrapper_id = $id;
    }

    public function get_id()
    {
        return $this->wrapper_id;
    }
}