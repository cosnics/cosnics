<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBar
{

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface>
     */
    private ArrayCollection $buttonCollection;

    /**
     * @var string[]
     */
    private array $classes;

    private ?string $searchUrl;

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface> $buttons
     * @param string[] $classes
     */
    public function __construct(
        ?string $searchUrl = null, ArrayCollection $buttons = new ArrayCollection(), array $classes = []
    )
    {
        $this->searchUrl = $searchUrl;
        $this->buttonCollection = $buttons;
        $this->classes = $classes;
    }

    public function addButton(ButtonInterface $button): static
    {
        $this->getButtonCollection()->add($button);

        return $this;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface> $buttons
     */
    public function addButtons(ArrayCollection $buttons = new ArrayCollection()): static
    {
        foreach ($buttons as $button)
        {
            $this->addButton($button);
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface>
     */
    public function getButtonCollection(): ArrayCollection
    {
        return $this->buttonCollection;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param string[] $classes
     */
    public function setClasses(array $classes): static
    {
        $this->classes = $classes;

        return $this;
    }

    public function getSearchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function setSearchUrl(?string $searchUrl): static
    {
        $this->searchUrl = $searchUrl;

        return $this;
    }

    public function hasButtons(): bool
    {
        return count($this->buttonCollection) > 0;
    }

    public function prependItem(ButtonInterface $button): static
    {
        array_unshift($this->buttonCollection, $button);

        return $this;
    }
}