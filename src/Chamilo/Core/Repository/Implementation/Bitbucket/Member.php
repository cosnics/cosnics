<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class Member
{

    private $username;

    private $first_name;

    private $last_name;

    private $email;

    /**
     *
     * @return the $username
     */
    public function get_username()
    {
        return $this->username;
    }

    /**
     *
     * @return the $first_name
     */
    public function get_first_name()
    {
        return $this->first_name;
    }

    /**
     *
     * @return the $last_name
     */
    public function get_last_name()
    {
        return $this->last_name;
    }

    /**
     *
     * @return the $email
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     *
     * @param $username the $username to set
     */
    public function set_username($username)
    {
        $this->username = $username;
    }

    /**
     *
     * @param $first_name the $first_name to set
     */
    public function set_first_name($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     *
     * @param $last_name the $last_name to set
     */
    public function set_last_name($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     *
     * @param $email the $email to set
     */
    public function set_email($email)
    {
        $this->email = $email;
    }
}
