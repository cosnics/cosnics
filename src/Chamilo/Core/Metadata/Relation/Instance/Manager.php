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
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'relation_instance_action';
    public const PARAM_RELATION_INSTANCE_ID = 'relation_instance_id';

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Relation[]
     */
    private $relations;

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    private $sourceEntities;

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    private $targetEntities;

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Relation[] $relations
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getSourceEntities()
    {
        return $this->sourceEntities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\EntityInterface[] $sourceEntities
     */
    public function setSourceEntities($sourceEntities)
    {
        $this->sourceEntities = $sourceEntities;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getTargetEntities()
    {
        return $this->targetEntities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\EntityInterface[] $targetEntities
     */
    public function setTargetEntities($targetEntities)
    {
        $this->targetEntities = $targetEntities;
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
