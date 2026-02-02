<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\MiniButtonCollectionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\MiniButtonCollectionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniButtonToolBar implements ButtonInterface, MiniButtonCollectionInterface
{
    use ButtonClassesTrait;
    use MiniButtonCollectionTrait;

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface> $buttons
     * @param string[] $classes
     */
    public function __construct(ArrayCollection $buttons = new ArrayCollection(), array $classes = [])
    {
        $this->setButtons($buttons);
        $this->setClasses($classes);
    }

    public function getButtonRendererClass(): string
    {
        return MiniButtonToolBarRenderer::class;
    }
}