<?php
namespace Chamilo\Core\Metadata\Entity;

/**
 *
 * @package Chamilo\Core\Metadata\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface EntityInterface
{

    /**
     *
     * @return string
     */
    public function getType();

    /**
     *
     * @return string
     */
    public function getIcon();

    /**
     *
     * @return string
     */
    public function getName();
}