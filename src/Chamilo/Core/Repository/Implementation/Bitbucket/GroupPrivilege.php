<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class GroupPrivilege
{

    private $group;

    private $name;

    private $privilege;

    private $repository;

    private $members;

    private $owner;

    public function get_group()
    {
        return $this->group;
    }

    public function set_group($group)
    {
        $this->group = $group;
    }

    public function get_privilege()
    {
        return $this->privilege;
    }

    public function set_privilege($privilege)
    {
        $this->privilege = $privilege;
    }

    public function get_repository()
    {
        return $this->repository;
    }

    public function set_repository($repository)
    {
        $this->repository = $repository;
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
     * @param $members the $members to set
     */
    public function set_members($members)
    {
        $this->members = $members;
    }

    /**
     *
     * @return the $name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param $name the $name to set
     */
    public function set_name($name)
    {
        $this->name = $name;
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
}
