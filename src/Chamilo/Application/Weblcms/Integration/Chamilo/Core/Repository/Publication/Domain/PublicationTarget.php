<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTarget extends \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
{
    /**
     * @var integer
     */
    private $courseIdentifier;

    /**
     * @var string
     */
    private $toolIdentifier;

    /**
     *
     * @var integer
     */
    private $userIdentifier;

    /**
     * @param string $modifierServiceIdentifier
     * @param integer $courseIdentifier
     * @param string $toolIdentifier
     * @param integer $userIdentifier
     */
    public function __construct($modifierServiceIdentifier, $courseIdentifier, $toolIdentifier, $userIdentifier)
    {
        parent:: __construct($modifierServiceIdentifier);

        $this->courseIdentifier = $courseIdentifier;
        $this->toolIdentifier = $toolIdentifier;
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @return integer
     */
    public function getCourseIdentifier(): int
    {
        return $this->courseIdentifier;
    }

    /**
     * @param integer $courseIdentifier
     */
    public function setCourseIdentifier(int $courseIdentifier): void
    {
        $this->courseIdentifier = $courseIdentifier;
    }

    /**
     * @return string
     */
    public function getToolIdentifier(): string
    {
        return $this->toolIdentifier;
    }

    /**
     * @param string $toolIdentifier
     */
    public function setToolIdentifier(string $toolIdentifier): void
    {
        $this->toolIdentifier = $toolIdentifier;
    }

    /**
     *
     * @return integer
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     *
     * @param integer $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }

}