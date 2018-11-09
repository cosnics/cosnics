<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Domain
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTarget extends \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
{
    /**
     *
     * @var integer
     */
    private $userIdentifier;

    /**
     *
     * @var integer
     */
    private $publicationIdentifier;

    /**
     * @param string $modifierServiceIdentifier
     * @param integer $userIdentifier
     * @param integer $publicationIdentifier
     */
    public function __construct($modifierServiceIdentifier, $userIdentifier, $publicationIdentifier)
    {
        parent:: __construct($modifierServiceIdentifier);

        $this->userIdentifier = $userIdentifier;
        $this->publicationIdentifier = $publicationIdentifier;
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

    /**
     *
     * @return integer
     */
    public function getPublicationIdentifier()
    {
        return $this->publicationIdentifier;
    }

    /**
     *
     * @param integer $publicationIdentifier
     */
    public function setPublicationIdentifier($publicationIdentifier)
    {
        $this->publicationIdentifier = $publicationIdentifier;
    }

}