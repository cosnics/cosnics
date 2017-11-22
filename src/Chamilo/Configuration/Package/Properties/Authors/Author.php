<?php
namespace Chamilo\Configuration\Package\Properties\Authors;

/**
 *
 * @author Hans De Bisschop
 * @package core.lynx
 */
class Author
{

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $email;

    /**
     *
     * @var string
     */
    private $company;

    /**
     *
     * @param string $name
     * @param string $email
     * @param string $company
     */
    public function __construct($name, $email, $company = null)
    {
        $this->set_name($name);
        $this->set_email($email);
        $this->set_company($company);
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     *
     * @param string $email
     */
    public function set_email($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @return string
     */
    public function get_company()
    {
        return $this->company;
    }

    /**
     *
     * @param string $company
     */
    public function set_company($company)
    {
        $this->company = $company;
    }
}
