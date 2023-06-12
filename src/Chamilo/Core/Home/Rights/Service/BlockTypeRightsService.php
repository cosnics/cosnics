<?php
namespace Chamilo\Core\Home\Rights\Service;

use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Renderer\BlockRendererFactory;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;

/**
 * Service to manage the rights for the given block types
 *
 * @package Chamilo\Core\Home\Rights\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockTypeRightsService
{

    protected BlockRendererFactory $blockRendererFactory;

    protected HomeRepository $homeRepository;

    protected RightsRepository $rightsRepository;

    public function __construct(
        RightsRepository $rightsRepository, HomeRepository $homeRepository, BlockRendererFactory $blockRendererFactory
    )
    {
        $this->rightsRepository = $rightsRepository;
        $this->homeRepository = $homeRepository;
        $this->blockRendererFactory = $blockRendererFactory;
    }

    /**
     * Checks whether or not a user can view the given block renderer, checking the target entities and checking
     * if the block is not read only and already added to the homepage
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function canUserViewBlockRenderer(User $user, BlockRenderer $blockRenderer): bool
    {
        if (!$this->canUserViewBlockType($user, get_class($blockRenderer)))
        {
            return false;
        }

        if ($blockRenderer->isReadOnly())
        {
            return true;
        }

        $userBlocks = $this->getHomeRepository()->findBlocksByUserIdentifier($user->getId());

        foreach ($userBlocks as $userBlock)
        {
            if ($userBlock->isBlock() && $userBlock->getBlockType() == get_class($blockRenderer) &&
                $userBlock->getContext() == $blockRenderer::CONTEXT)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether or not a user can view the given block type
     *
     * @param User $user
     * @param string $blockType
     *
     * @return bool
     */
    public function canUserViewBlockType(User $user, string $blockType): bool
    {
        if ($user->isPlatformAdmin())
        {
            return true;
        }

        $targetedBlockTypes = $this->getRightsRepository()->findTargetedBlockTypes();

        if (!in_array($blockType, $targetedBlockTypes))
        {
            return true;
        }

        $blockTypesForUser = $this->getRightsRepository()->findBlockTypesTargetedForUser($user);

        return in_array($blockType, $blockTypesForUser);
    }

    public function getBlockRendererFactory(): BlockRendererFactory
    {
        return $this->blockRendererFactory;
    }

    /**
     * Returns a list of block types with target entities
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getBlockTypesWithTargetEntities(): array
    {
        $targetEntitiesPerBlockType = $this->getTargetEntitiesPerBlockType();

        $blockTypesWithTargetEntities = [];

        $blockTypes = $this->getBlockRendererFactory()->getAvailableBlockRendererTypes();

        foreach ($blockTypes as $blockType)
        {
            $blockTypeWithTargetEntity = [];
            $blockTypeWithTargetEntity['block_type'] = $blockType;

            if (array_key_exists($blockType, $targetEntitiesPerBlockType))
            {
                $targetEntities = $targetEntitiesPerBlockType[$blockType];
                foreach ($targetEntities as $targetEntity)
                {
                    $blockTypeWithTargetEntity['target_entities'][$targetEntity->get_entity_type()][] =
                        $targetEntity->get_entity_id();
                }
            }

            $blockTypesWithTargetEntities[] = $blockTypeWithTargetEntity;
        }

        return $blockTypesWithTargetEntities;
    }

    public function getHomeRepository(): HomeRepository
    {
        return $this->homeRepository;
    }

    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    /**
     * @param string $blockType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetEntitiesForBlockType(string $blockType): ArrayCollection
    {
        return $this->getRightsRepository()->findTargetEntitiesForBlockType($blockType);
    }

    /**
     * Helper function to get the target entities grouped per block type
     *
     * @return \Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function getTargetEntitiesPerBlockType(): array
    {
        $targetEntitiesPerBlockType = [];

        $blockTypeTargetEntities = $this->getRightsRepository()->findBlockTypeTargetEntities();

        foreach ($blockTypeTargetEntities as $blockTypeTargetEntity)
        {
            $targetEntitiesPerBlockType[$blockTypeTargetEntity->get_block_type()][] = $blockTypeTargetEntity;
        }

        return $targetEntitiesPerBlockType;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function setTargetEntitiesForBlockType(string $blockType, array $targetEntities = []): void
    {
        if (!$this->getRightsRepository()->clearTargetEntitiesForBlockType($blockType))
        {
            throw new RuntimeException('Failed to delete the target entities for block type ' . $blockType);
        }

        foreach ($targetEntities as $targetEntityType => $targetEntityIdentifiers)
        {
            foreach ($targetEntityIdentifiers as $targetEntityIdentifier)
            {
                $blockTypeTargetEntity = new BlockTypeTargetEntity();
                $blockTypeTargetEntity->set_block_type($blockType);
                $blockTypeTargetEntity->set_entity_type($targetEntityType);
                $blockTypeTargetEntity->set_entity_id($targetEntityIdentifier);

                if (!$this->getRightsRepository()->createBlockTypeTargetEntity($blockTypeTargetEntity))
                {
                    throw new RuntimeException(
                        sprintf(
                            'Could not create a new $blockType target entity for $blockType %s, ' .
                            'entity type %s and entity id %s', $blockType, $targetEntityType, $targetEntityIdentifier
                        )
                    );
                }
            }
        }
    }
}