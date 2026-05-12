<?php
namespace Chamilo\Core\Admin\Implementation\Home;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Core\Admin\Implementation\Home
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class PortalHomeBlockRenderer extends BlockRenderer
{
    public const string CONTEXT = Manager::CONTEXT;

    public function displayContent(Element $block, ?User $user = null): string
    {
        return '';
    }
}
