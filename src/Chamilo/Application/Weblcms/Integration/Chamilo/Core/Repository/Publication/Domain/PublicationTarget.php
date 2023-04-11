<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTarget extends \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
{
    private string $courseIdentifier;

    private string $toolIdentifier;

    public function __construct(
        string $modifierServiceIdentifier, string $courseIdentifier, string $toolIdentifier, string $userIdentifier
    )
    {
        parent:: __construct($modifierServiceIdentifier, $userIdentifier);

        $this->courseIdentifier = $courseIdentifier;
        $this->toolIdentifier = $toolIdentifier;
    }

    public function getCourseIdentifier(): string
    {
        return $this->courseIdentifier;
    }

    public function getToolIdentifier(): string
    {
        return $this->toolIdentifier;
    }

    public function setCourseIdentifier(string $courseIdentifier): void
    {
        $this->courseIdentifier = $courseIdentifier;
    }

    public function setToolIdentifier(string $toolIdentifier): void
    {
        $this->toolIdentifier = $toolIdentifier;
    }

}