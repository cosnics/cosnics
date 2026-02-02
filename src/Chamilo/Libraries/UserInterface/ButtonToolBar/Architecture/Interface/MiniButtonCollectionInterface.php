<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface MiniButtonCollectionInterface extends ButtonInterface
{
    public function addButton(Button $button): static;

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button> $buttons
     */
    public function addButtons(ArrayCollection $buttons): static;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button>
     */
    public function getButtons(): ArrayCollection;

    public function hasButtons(): bool;

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button> $buttons
     */
    public function setButtons(ArrayCollection $buttons): static;
}
