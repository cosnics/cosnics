<?php

namespace Chamilo\Application\Lti\Domain\Role;

/**
 * Class SystemRole
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class Role
{
    /**
     * @var string
     */
    protected $role;

    /**
     * ContextType constructor.
     *
     * @param string $role
     */
    public function __construct(string $role)
    {
        if (!in_array($role, $this->getAvailableRoles()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given role %s is not valid. The role should be one of (%s)',
                    $role, implode(', ', $this->getAvailableRoles())
                )
            );
        }

        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return array
     */
    abstract public function getAvailableRoles();
}