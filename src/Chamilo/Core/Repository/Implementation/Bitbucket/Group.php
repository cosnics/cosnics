<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class Group
{

    private $name;

    private $permission;

    private $members;

    private $slug;

    private $owner;

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_slug()
    {
        return $this->slug;
    }

    public function set_slug($slug)
    {
        $this->slug = $slug;
    }

    /**
     *
     * @return the $permission
     */
    public function get_permission()
    {
        return $this->permission;
    }

    /**
     *
     * @return the $members
     */
    public function get_members()
    {
        return $this->members;
    }

    /**
     *
     * @param $permission the $permission to set
     */
    public function set_permission($permission)
    {
        $this->permission = $permission;
    }

    /**
     *
     * @param $members the $members to set
     */
    public function set_members($members)
    {
        $this->members = $members;
    }

    /**
     *
     * @return the $owner
     */
    public function get_owner()
    {
        return $this->owner;
    }

    /**
     *
     * @param $owner the $owner to set
     */
    public function set_owner($owner)
    {
        $this->owner = $owner;
    }

    public function get_owner_username()
    {
        return $this->get_owner()->username;
    }

    public function get_id()
    {
        return $this->get_owner_username() . '/' . $this->get_slug();
    }
}
