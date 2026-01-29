<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonGroupButtonsInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonGroupButtonsTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonGroupRenderer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonGroup implements ButtonInterface, ButtonGroupButtonsInterface
{
    use ButtonClassesTrait;
    use ButtonGroupButtonsTrait;

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton|\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton> $groupButtons
     * @param string[] $classes
     */
    public function __construct(ArrayCollection $groupButtons = new ArrayCollection(), array $classes = [])
    {
        $this->setGroupButtons($groupButtons);
        $this->setClasses($classes);
    }

    public function getButtonRendererClass(): string
    {
        return ButtonGroupRenderer::class;
    }
}