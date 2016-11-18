<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class Privilege
{

    private $username;

    private $first_name;

    private $last_name;

    private $privilege;

    private $repository;

    public function get_username()
    {
        return $this->username;
    }

    public function set_username($username)
    {
        $this->username = $username;
    }

    public function get_first_name()
    {
        return $this->first_name;
    }

    public function set_first_name($first_name)
    {
        $this->first_name = $first_name;
    }

    public function get_last_name()
    {
        return $this->last_name;
    }

    public function set_last_name($last_name)
    {
        $this->last_name = $last_name;
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
}
