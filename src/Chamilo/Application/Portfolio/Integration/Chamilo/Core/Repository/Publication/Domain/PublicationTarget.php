<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTarget extends \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
{
    private string $publicationIdentifier;

    public function __construct(string $modifierServiceIdentifier, string $userIdentifier, string $publicationIdentifier)
    {
        parent:: __construct($modifierServiceIdentifier, $userIdentifier);

        $this->publicationIdentifier = $publicationIdentifier;
    }

    public function getPublicationIdentifier(): string
    {
        return $this->publicationIdentifier;
    }

    public function setPublicationIdentifier(string $publicationIdentifier)
    {
        $this->publicationIdentifier = $publicationIdentifier;
    }

}