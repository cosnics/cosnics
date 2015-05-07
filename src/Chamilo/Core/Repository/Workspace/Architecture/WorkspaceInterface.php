<?php
namespace Chamilo\Core\Repository\Workspace\Architecture;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface WorkspaceInterface
{

    /**
     *
     * @return string
     */
    public function getName();

    /**
     *
     * @param string $name
     */
    public function setName($name);

    /**
     *
     * @return string
     */
    public function getDescription();

    /**
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     *
     * @return int
     */
    public function getCreatorId();

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getCreator();

    /**
     *
     * @param int $creatorId
     */
    public function setCreatorId($creatorId);
}