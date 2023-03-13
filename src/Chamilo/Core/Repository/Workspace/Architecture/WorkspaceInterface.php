<?php
namespace Chamilo\Core\Repository\Workspace\Architecture;

/**
 * @package Chamilo\Core\Repository\Workspace\Architecture
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface WorkspaceInterface
{

    public function getCreatorId(): ?string;

    public function getHash(): string;

    public function getId(): ?string;

    public function getTitle(): string;

    public function getWorkspaceType(): int;
}