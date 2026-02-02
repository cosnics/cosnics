<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonGroupCollectionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonGroupCollectionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonGroupRenderer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonGroup implements ButtonInterface, ButtonGroupCollectionInterface
{
    use ButtonClassesTrait;
    use ButtonGroupCollectionTrait;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection|\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection> $groupButtons
     * @param string[] $classes
     */
    public function __construct(ArrayCollection $groupButtons = new ArrayCollection(), array $classes = [])
    {
        $this->setButtons($groupButtons);
        $this->setClasses($classes);
    }

    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonGroupRenderer>
     */
    public function getButtonRendererClass(): string
    {
        return ButtonGroupRenderer::class;
    }
}