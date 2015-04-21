<?php
namespace Chamilo\Core\Metadata\Entity;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Metadata\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DataClassEntity implements EntityInterface
{
    // Identifier types
    const IDENTIFIER_TYPE_INSTANCE = 0;
    const IDENTIFIER_ALL_INSTANCES = null;

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
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $dataClass;

    /**
     *
     * @param $dataClassName
     * @param integer $dataClassIdentifier
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     */
    public function __construct($dataClassName, $dataClassIdentifier = 0, DataClass $dataClass = null)
    {
        $this->dataClassName = $dataClassName;
        $this->dataClassIdentifier = $dataClassIdentifier;
        $this->dataClass = $dataClass;
    }

    /**
     *
     * @return string
     */
    public function getDataClassName()
    {
        return $this->dataClassName;
    }

    /**
     *
     * @param string $dataClassName
     */
    public function setDataClassName($dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }

    /**
     *
     * @return integer
     */
    public function getDataClassIdentifier()
    {
        return $this->dataClassIdentifier;
    }

    /**
     *
     * @param integer $dataClassIdentifier
     */
    public function setDataClassIdentifier($dataClassIdentifier)
    {
        $this->dataClassIdentifier = $dataClassIdentifier;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getDataClass()
    {
        if ($this->isDataClassIdentified() && ! isset($this->dataClass))
        {
            $this->dataClass = $this->getDataClassObject($this->dataClassName, $this->dataClassIdentifier);
        }

        return $this->dataClass;
    }

    /**
     *
     * @param string $dataClassName
     * @param integer $dataClassIdentifier
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getDataClassObject($dataClassName, $dataClassIdentifier)
    {
        return DataManager :: retrieve_by_id($dataClassName, $dataClassIdentifier);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     *
     * @return boolean
     */
    public function isDataClassIdentified()
    {
        return $this->getDataClassIdentifier() != 0;
    }

    /**
     *
     * @return string
     */
    public abstract function getType();

    /**
     *
     * @return string
     */
    public abstract function getIcon();

    /**
     *
     * @return string
     */
    public function getName()
    {
        $dataClass = $this->getDataClass();

        if ($dataClass instanceof DataClass)
        {
            return $this->getDisplayName();
        }
        else
        {
            return $this->getType();
        }
    }

    /**
     *
     * @return string
     */
    public abstract function getDisplayName();
}