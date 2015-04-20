<?php
namespace Chamilo\Core\Repository\ContentObject\File\Integration\Chamilo\Core\Metadata\Entity;

use Chamilo\Core\Metadata\Entity\EntityInterface;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Integration\Chamilo\Core\Metadata\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FileEntity implements EntityInterface
{

    /**
     *
     * @var string
     */
    private $dataClassName;

    /**
     *
     * @var integer
     */
    private $dataClassIdentifier;

    /**
     *
     * @param string $dataClassName
     * @param integer $dataClassIdentifier
     */
    public function __construct($dataClassName, $dataClassIdentifier = 0)
    {
        $this->dataClassName = $dataClassName;
        $this->dataClassIdentifier = $dataClassIdentifier;
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\EntityInterface::getDataClassName()
     */
    public function getDataClassName()
    {
        return $this->dataClassName;
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\EntityInterface::getDataClassIdentifier()
     */
    public function getDataClassIdentifier()
    {
        return $this->dataClassIdentifier;
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