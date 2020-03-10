<?php
namespace Chamilo\Core\Metadata\Relation\Instance;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use InvalidArgumentException;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'relation_instance_action';
    const PARAM_RELATION_INSTANCE_ID = 'relation_instance_id';
    
    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     *
     * @var \Chamilo\Core\Metadata\Interfaces\EntityInterface[]
     */
    private $sourceEntities;

    /**
     *
     * @var \Chamilo\Core\Metadata\Interfaces\EntityInterface[]
     */
    private $targetEntities;

    /**
     *
     * @var \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation[]
     */
    private $relations;

    /**
     *
     * @return \Chamilo\Core\Metadata\Interfaces\EntityInterface[]
     */
    public function getSourceEntities()
    {
        return $this->sourceEntities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Interfaces\EntityInterface[] $sourceEntities
     */
    public function setSourceEntities($sourceEntities)
    {
        $this->sourceEntities = $sourceEntities;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Interfaces\EntityInterface[]
     */
    public function getTargetEntities()
    {
        return $this->targetEntities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Interfaces\EntityInterface[] $targetEntities
     */
    public function setTargetEntities($targetEntities)
    {
        $this->targetEntities = $targetEntities;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation[] $relations
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;
    }

    public function verifySetup()
    {
        $sourceEntityCount = count($this->getSourceEntities());
        $targetEntityCount = count($this->getTargetEntities());
        $relationCount = count($this->getRelations());
        
        if ($sourceEntityCount == 0 && $targetEntityCount == 0 && $relationCount == 0)
        {
            throw new InvalidArgumentException(Translation::get('VerifyEntitiesRelationsSetup'));
        }
    }
}
