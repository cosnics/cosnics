<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication;

/**
 *
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Location extends \Chamilo\Core\Repository\Publication\Location\Location
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
     *
     * @param string $context
     * @param string $name
     * @param integer $userIdentifier
     * @param integer $publicationIdentifier
     */
    function __construct($context, $name, $userIdentifier, $publicationIdentifier)
    {
        parent :: __construct($context, $name);
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