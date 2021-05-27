<?php
namespace Chamilo\Core\Home\Rights\Service;

use Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use RuntimeException;

/**
 * Service to manage the rights for the given block types
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeRightsService
{

    /**
     *
     * @var RightsRepository
     */
    protected $rightsRepository;

    /**
     *
     * @var HomeRepository
     */
    protected $homeRepository;

    /**
     * BlockTypeRightsService constructor.
     * 
     * @param RightsRepository $rightsRepository
     */
    public function __construct(RightsRepository $rightsRepository, HomeRepository $homeRepository)
    {
        $this->rightsRepository = $rightsRepository;
        $this->homeRepository = $homeRepository;
    }

    /**
     * Sets the target entities for a given element
     * 
     * @param string $blockType
     * @param array $targetEntities
     */
    public function setTargetEntitiesForBlockType($blockType, $targetEntities = [])
    {
        if (! $this->rightsRepository->clearTargetEntitiesForBlockType($blockType))
        {
            throw new RuntimeException('Failed to delete the target entities for block type ' . $blockType);
        }
        
        foreach ($targetEntities as $targetEntityType => $targetEntityIdentifiers)
        {
            foreach ($targetEntityIdentifiers as $targetEntityIdentifier)
            {
                $elementTargetEntity = new BlockTypeTargetEntity();
                $elementTargetEntity->set_block_type($blockType);
                $elementTargetEntity->set_entity_type($targetEntityType);
                $elementTargetEntity->set_entity_id($targetEntityIdentifier);
                
                if (! $elementTargetEntity->create())
                {
                    throw new RuntimeException(
                        sprintf(
                            'Could not create a new $blockType target entity for $blockType %s, ' .
                                 'entity type %s and entity id %s', 
                                $blockType, 
                                $targetEntityType, 
                                $targetEntityIdentifier));
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
    public function getTargetEntitiesForBlockType($blockType)
    {
        return $this->rightsRepository->findTargetEntitiesForBlockType($blockType);
    }

    /**
     * Checks whether or not a user can view the given block type
     * 
     * @param User $user
     * @param string $blockType
     *
     * @return bool
     */
    public function canUserViewBlockType(User $user, $blockType)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }
        
        $targetedBlockTypes = $this->rightsRepository->findTargetedBlockTypes();
        
        if (! in_array($blockType, $targetedBlockTypes))
        {
            return true;
        }
        
        $blockTypesForUser = $this->rightsRepository->findBlockTypesTargetedForUser($user);
        
        return in_array($blockType, $blockTypesForUser);
    }

    /**
     * Checks whether or not a user can view the given block renderer, checking the target entities and checking
     * if the block is not deletable and already added to the homepage
     * 
     * @param User $user
     * @param BlockRenderer $blockRenderer
     *
     * @return bool
     */
    public function canUserViewBlockRenderer(User $user, BlockRenderer $blockRenderer)
    {
        if (! $this->canUserViewBlockType($user, get_class($blockRenderer)))
        {
            return false;
        }
        
        if ($blockRenderer->isDeletable())
        {
            return true;
        }
        
        $classNameUtilities = ClassnameUtilities::getInstance();
        $blockClass = get_class($blockRenderer);
        $blockClassName = $classNameUtilities->getClassnameFromNamespace($blockClass);
        $blockClassContext = $classNameUtilities->getNamespaceParent($blockClass, 6);
        
        $userBlocks = $this->homeRepository->findBlocksByUserIdentifier($user->getId());
        foreach($userBlocks as $userBlock)
        {
            
            /** @var Block $userBlock */
            if ($userBlock->getBlockType() == $blockClassName && $userBlock->getContext() == $blockClassContext)
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Returns a list of block types with target entities
     */
    public function getBlockTypesWithTargetEntities()
    {
        $targetEntitiesPerBlockType = $this->getTargetEntitiesPerBlockType();
        
        $blockTypesWithTargetEntities = [];
        
        $blockTypes = $this->homeRepository->findBlockTypes();
        foreach ($blockTypes as $blockType)
        {
            $blockTypeWithTargetEntity = [];
            $blockTypeWithTargetEntity['block_type'] = $blockType;
            
            if (array_key_exists($blockType, $targetEntitiesPerBlockType))
            {
                $targetEntities = $targetEntitiesPerBlockType[$blockType];
                foreach ($targetEntities as $targetEntity)
                {
                    $blockTypeWithTargetEntity['target_entities'][$targetEntity->get_entity_type()][] = $targetEntity->get_entity_id();
                }
            }
            
            $blockTypesWithTargetEntities[] = $blockTypeWithTargetEntity;
        }
        
        return $blockTypesWithTargetEntities;
    }

    /**
     * Helper function to get the target entities grouped per block type
     * 
     * @return BlockTypeTargetEntity[][]
     */
    protected function getTargetEntitiesPerBlockType()
    {
        $targetEntitiesPerBlockType = [];
        
        $blockTypeTargetEntities = $this->rightsRepository->findBlockTypeTargetEntities();
        
        foreach ($blockTypeTargetEntities as $blockTypeTargetEntity)
        {
            $targetEntitiesPerBlockType[$blockTypeTargetEntity->get_block_type()][] = $blockTypeTargetEntity;
        }
        
        return $targetEntitiesPerBlockType;
    }
}