<?php
namespace Chamilo\Core\Metadata\Entity;

/**
 *
 * @package Chamilo\Core\Metadata\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface EntityInterface
{

    /**
     *
     * @param string $dataClassName
     * @param integer $dataClassIdentifier
     */
    public function __construct($dataClassName, $dataClassIdentifier = 0);

    /**
     *
     * @return string
     */
    public function getDataClassName();

    /**
     *
     * @return integer
     */
    public function getDataClassIdentifier();

    public function getType();

    public function getIcon();

    public function getName();
}