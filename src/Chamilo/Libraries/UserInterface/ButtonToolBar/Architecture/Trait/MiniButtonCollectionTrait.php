<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait MiniButtonCollectionTrait
{
    use ButtonCollectionTrait {
        addButton as public addBaseButton;
        addButtons as public addBaseButtons;
        getButtons as public getBaseButtons;
        hasButtons as public hasBaseButtons;
        setButtons as public setBaseButtons;
    }

    public function addButton(Button $button): static
    {
        $this->addBaseButton($button);

        return $this;
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button> $buttons
     */
    public function addButtons(ArrayCollection $buttons): static
    {
        return $this->addBaseButtons($buttons);
    }

    /**
     * @return ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button>
     */
    public function getButtons(): ArrayCollection
    {
        return $this->getBaseButtons();
    }

    public function hasButtons(): bool
    {
        return $this->hasBaseButtons();
    }

    /**
     * @param ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button> $buttons
     */
    public function setButtons(ArrayCollection $buttons): static
    {
        $this->setBaseButtons($buttons);

        return $this;
    }
}
