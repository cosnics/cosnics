<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Domain
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
     * @param string $modifierServiceIdentifier
     * @param integer $userIdentifier
     */
    public function __construct($modifierServiceIdentifier, $userIdentifier)
    {
        parent:: __construct($modifierServiceIdentifier);

        $this->userIdentifier = $userIdentifier;
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