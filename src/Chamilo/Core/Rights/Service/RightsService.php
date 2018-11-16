<?php
namespace Chamilo\Core\Rights\Service;

use Chamilo\Core\Rights\Domain\RightsLocation;
use Chamilo\Core\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Core\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Service\UserService;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Rights\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RightsService
{
    // Types
    const TREE_TYPE_ROOT = 0;
    const TYPE_ROOT = 0;

    /**
     * @var \Chamilo\Core\Rights\Storage\Repository\RightsRepository
     */
    private $rightsRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var integer[]
     */
    private $locationIdentifiersCache;

    /**
     * @param \Chamilo\Core\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator
    )
    {
        $this->rightsRepository = $rightsRepository;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param integer $type
     * @param integer $identifier
     * @param integer $inherit
     * @param integer $parent
     * @param integer $locked
     * @param integer $treeIdentifier
     * @param integer $treeType
     * @param boolean $returnLocation
     * @param boolean $createInBatch
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     * @see RightsUtil::create_location()
     */
    public function createRightsLocationFromParameters(
        int $type = self::TYPE_ROOT, int $identifier = 0, int $inherit = 0, int $parent = 0, int $locked = 0,
        int $treeIdentifier = 0, int $treeType = self::TREE_TYPE_ROOT, bool $returnLocation = false,
        bool $createInBatch = false
    )
    {
        $location = $this->getRightsLocationInstance();
        $location->set_parent_id($parent);
        $location->set_type($type);
        $location->set_identifier($identifier);
        $location->set_inherit($inherit);
        $location->set_locked($locked);
        $location->set_tree_identifier($treeIdentifier);
        $location->set_tree_type($treeType);

        $success = $this->createRightsLocation($location, $createInBatch);
        $succes = $location->create(null, $createInBatch);

        if ($returnLocation && $success)
        {
            return $location;
        }
        else
        {
            return $succes;
        }
    }

    /**
     * @param integer $userIdentifier
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return boolean
     * @see RightsUtil::is_allowed_on_location()
     */
    public function doesUserIdentifierHaveRightForLocationAndEntities(
        int $userIdentifier, int $right, RightsLocation $location, array $entities
    )
    {
        $grantedRights = $this->findGrantedRightsForUserIdentifierLocationAndEntities(
            $userIdentifier, $location, $entities
        );

        return in_array($right, $grantedRights);
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return integer[]
     * @see DataManager::retrieve_granted_rights_array()
     */
    public function findGrantedRightsForUserIdentifierLocationAndEntities(
        int $userIdentifier, RightsLocation $location, array $entities
    )
    {
        $grantedRights = $this->getRightsRepository()->findGrantedRightsForUserIdentifierLocationAndEntities(
            $userIdentifier, $location, $entities
        );

        if ($location->inherits())
        {
            $parentLocation =
                $this->getRightsRepository()->findRightsLocationByIdentifier($location->get_parent_id());

            if ($parentLocation instanceof RightsLocation)
            {
                $parentRights = $this->findGrantedRightsForUserIdentifierLocationAndEntities(
                    $userIdentifier, $parentLocation, $entities
                );
                $grantedRights = array_merge($grantedRights, $parentRights);
            }
        }

        return array_unique($grantedRights);
    }

    /**
     * @param integer $userIdentifier
     * @param integer $right
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return boolean
     * @throws \Chamilo\Core\Rights\Exception\RightsLocationNotFoundException
     * @see RightsUtil::is_allowed()
     */
    public function doesUserIdentifierHaveRightForEntitiesAndLocationIdentifiers(
        int $userIdentifier, int $right, array $entities, int $identifier = 0, int $type = self::TYPE_ROOT,
        int $treeIdentifier = 0, int $treeType = self::TREE_TYPE_ROOT
    )
    {
        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($user->is_platform_admin())
        {
            return true;
        }

        $location = $this->getRightsRepository()->findRightsLocationByParameters(
            $identifier, $type, $treeIdentifier, $treeType
        );

        if (!$location instanceof RightsLocation)
        {
            // todo: refactor to translation
            throw new RightsLocationNotFoundException(
                $this->getTranslator()->trans('NoLocationFound') . ';type=' . $type . ';identifier=' . $identifier .
                ';tree_id=' . $treeIdentifier . ';tree_type=' . $treeType
            );
        }

        return $this->doesUserIdentifierHaveRightForLocationAndEntities(
            $userIdentifier, $right, $location, $entities
        );
    }

    /**
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $parentLocation
     * @param integer $type
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return integer[]
     * @see RightsUtil::get_identifiers_with_right_granted()
     */
    public function findRightsLocationIdentifiersWithGrantedRight(
        int $right, RightsLocation $parentLocation, int $type, int $userIdentifier, array $entities
    )
    {
        $cacheKey = md5(serialize([$userIdentifier, $right, $parentLocation->getId()]));

        if (is_null($this->locationIdentifiersCache[$cacheKey]))
        {
            $parentHasRight = $this->doesUserIdentifierHaveRightForLocationAndEntities(
                $userIdentifier, $right, $parentLocation, $entities
            );

            $this->locationIdentifiersCache[$cacheKey] =
                $this->getRightsRepository()->findRightsLocationIdentifiersWithGrantedRight(
                    $right, $parentLocation, $type, $userIdentifier, $entities, $parentHasRight
                );
        }

        return $this->locationIdentifiersCache[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param boolean $createInBatch
     *
     * @return boolean
     */
    public function createRightsLocation(RightsLocation $location, bool $createInBatch = false)
    {
        return $location->create(null, $createInBatch);
    }

    /**
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     */
    public function findRightsLocationByParameters(
        int $identifier = 0, int $type = RightsService::TYPE_ROOT, int $treeIdentifier = 0,
        int $treeType = RightsService::TREE_TYPE_ROOT
    )
    {
        return $this->getRightsRepository()->findRightsLocationByParameters(
            $identifier, $type, $treeIdentifier, $treeType
        );
    }

    /**
     * @return \Chamilo\Core\Rights\Domain\RightsLocationEntityRight
     */
    abstract public function getRightsLocationEntityRightInstance();

    /**
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     */
    abstract public function getRightsLocationInstance();

    /**
     * @return \Chamilo\Core\Rights\Storage\Repository\RightsRepository
     */
    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    /**
     * @param \Chamilo\Core\Rights\Storage\Repository\RightsRepository $rightsRepository
     */
    public function setRightsRepository(RightsRepository $rightsRepository): void
    {
        $this->rightsRepository = $rightsRepository;
    }
}