<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonDividerRenderer;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonDivider implements ButtonInterface
{
    use ButtonClassesTrait;

    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonDividerRenderer>
     */
    public function getButtonRendererClass(): string
    {
        return SubButtonDividerRenderer::class;
    }
}