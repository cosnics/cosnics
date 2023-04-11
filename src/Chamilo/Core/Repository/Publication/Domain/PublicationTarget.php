<?php
namespace Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Core\Repository\Publication\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTarget
{

    protected string $modifierServiceIdentifier;

    protected string $userIdentifier;

    public function __construct(string $modifierServiceIdentifier, string $userIdentifier)
    {
        $this->modifierServiceIdentifier = $modifierServiceIdentifier;
        $this->userIdentifier = $userIdentifier;
    }

    public function getModifierServiceIdentifier(): string
    {
        return $this->modifierServiceIdentifier;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function setModifierServiceIdentifier(string $modifierServiceIdentifier): void
    {
        $this->modifierServiceIdentifier = $modifierServiceIdentifier;
    }

    public function setUserIdentifier(string $userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }
}