<?php

namespace Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Core\Repository\Publication\Domain
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTarget
{
    /**
     * @var string
     */
    private $modifierServiceIdentifier;

    /**
     * @param string $modifierServiceIdentifier
     */
    public function __construct($modifierServiceIdentifier)
    {
        $this->modifierServiceIdentifier = $modifierServiceIdentifier;
    }

    /**
     * @return string
     */
    public function getModifierServiceIdentifier(): string
    {
        return $this->modifierServiceIdentifier;
    }

    /**
     * @param string $modifierServiceIdentifier
     */
    public function setModifierServiceIdentifier(string $modifierServiceIdentifier): void
    {
        $this->modifierServiceIdentifier = $modifierServiceIdentifier;
    }
}