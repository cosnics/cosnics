<?php
namespace Chamilo\Core\Metadata\Integration\Chamilo\Core\Metadata\Entity;

use Chamilo\Core\Metadata\Entity\EntityInterface;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Integration\Chamilo\Core\Metadata\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaEntity implements EntityInterface
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
        return Translation :: getInstance()->getTranslation(
            'TypeName',
            null,
            'Chamilo\Core\Repository\ContentObject\File');
    }

    public function getIcon()
    {
        return Theme :: getInstance()->getImage('Logo', 'png');
    }

    public function getName()
    {
    }
}