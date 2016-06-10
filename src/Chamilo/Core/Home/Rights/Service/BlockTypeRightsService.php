<?php

namespace Chamilo\Core\Home\Rights\Service;

use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to manage the rights for the given block types
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeRightsService
{
    /**
     * @var RightsRepository
     */
    protected $rightsRepository;

    /**
     * BlockTypeRightsService constructor.
     *
     * @param RightsRepository $rightsRepository
     */
    public function __construct(RightsRepository $rightsRepository)
    {
        $this->rightsRepository = $rightsRepository;
    }

    /**
     * Sets the target entities for a given element
     *
     * @param string $blockType
     * @param array $targetEntities
     */
    public function setTargetEntitiesForBlockType($blockType, $targetEntities = array())
    {
        if (!$this->rightsRepository->clearTargetEntitiesForBlockType($blockType))
        {
            throw new \RuntimeException('Failed to delete the target entities for block type ' . $blockType);
        }

        foreach ($targetEntities as $targetEntityType => $targetEntityIdentifiers)
        {
            foreach ($targetEntityIdentifiers as $targetEntityIdentifier)
            {
                $elementTargetEntity = new BlockTypeTargetEntity();
                $elementTargetEntity->set_block_type($blockType);
                $elementTargetEntity->set_entity_type($targetEntityType);
                $elementTargetEntity->set_entity_id($targetEntityIdentifier);

                if (!$elementTargetEntity->create())
                {
                    throw new \RuntimeException(
                        sprintf(
                            'Could not create a new $blockType target entity for $blockType %s, ' .
                            'entity type %s and entity id %s',
                            $blockType, $targetEntityType, $targetEntityIdentifier
                        )
                    );
                }
            }
        }
    }

    /**
     * Returns the target entities for a given block type
     *
     * @param string $blockType
     *
     * @return BlockTypeTargetEntity[]
     */
    public function getTargetEntitiesForElement($blockType)
    {
        return $this->rightsRepository->findTargetEntitiesForBlockType($blockType);
    }

    /**
     * Checks whether or not a user can view the given element
     *
     * @param User $user
     * @param string $blockType
     *
     * @return bool
     */
    public function canUserViewBlockType(User $user, $blockType)
    {
        $targetedBlockTypes = $this->rightsRepository->findTargetedBlockTypes();

        if(!in_array($blockType, $targetedBlockTypes))
        {
            return true;
        }

        $blockTypesForUser = $this->rightsRepository->findBlockTypesTargetedForUser($user);

        return in_array($blockType, $blockTypesForUser);
    }
}