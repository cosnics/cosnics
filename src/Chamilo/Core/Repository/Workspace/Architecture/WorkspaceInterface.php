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
     * @return integer
     */
    public function getId();

    /**
     *
     * @return integer
     */
    public function getCreatorId();

    /**
     *
     * @return integer
     */
    public function getWorkspaceType();

    /**
     *
     * @return string
     */
    public function getTitle();

    /**
     *
     * @return string
     */
    public function getHash();
}