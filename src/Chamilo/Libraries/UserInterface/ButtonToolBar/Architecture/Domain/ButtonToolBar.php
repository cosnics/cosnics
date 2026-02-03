<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonCollectionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonCollectionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBar implements ButtonInterface, ButtonCollectionInterface
{
    use ButtonClassesTrait;
    use ButtonCollectionTrait;

    private ?string $searchUrl;

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface> $buttons
     * @param string[] $classes
     */
    public function __construct(
        ?string $searchUrl = null, ArrayCollection $buttons = new ArrayCollection(), array $classes = []
    )
    {
        $this->setButtons($buttons);
        $this->setClasses($classes);

        $this->searchUrl = $searchUrl;
    }

    public function getButtonRendererClassName(): string
    {
        return ButtonToolBarRenderer::class;
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
}