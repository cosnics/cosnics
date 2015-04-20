<?php
namespace Chamilo\Core\Metadata\Entity;

/**
 *
 * @package Chamilo\Core\Metadata\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityType implements EntityInterface
{

    /**
     *
     * @var string
     */
    private $className;

    /**
     *
     * @param string $className
     * @param int $identifier
     */
    public function __construct($className, $id = 0)
    {
        $this->className = $className;
    }

    public function getDataClassName()
    {
    }

    public function getDataClassIdentifier()
    {
    }

    public function getType()
    {
    }

    public function getIcon()
    {
    }

    public function getName()
    {
    }
}