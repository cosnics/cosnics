<?php
namespace Chamilo\Configuration\Package\Properties\Authors;

/**
 * @package Chamilo\Configuration\Package\Properties\Authors
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Author
{

    private ?string $company;

    private string $email;

    private string $name;

    public function __construct(string $name, string $email, ?string $company = null)
    {
        $this->set_name($name);
        $this->set_email($email);
        $this->set_company($company);
    }

    public function get_company(): ?string
    {
        return $this->company;
    }

    public function get_email(): string
    {
        return $this->email;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function set_company(?string $company)
    {
        $this->company = $company;
    }

    public function set_email(string $email)
    {
        $this->email = $email;
    }

    public function set_name(string $name)
    {
        $this->name = $name;
    }
}
