<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonRendererCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererCollectionTrait
{
    protected ButtonRendererCollection $buttonRendererCollection;

    public function getButtonRendererCollection(): ButtonRendererCollection
    {
        return $this->buttonRendererCollection;
    }

    public function setButtonRendererCollection(ButtonRendererCollection $buttonRendererCollection): static
    {
        $this->buttonRendererCollection = $buttonRendererCollection;

        return $this;
    }
}