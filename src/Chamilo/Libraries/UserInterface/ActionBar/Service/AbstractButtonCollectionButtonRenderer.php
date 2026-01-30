<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonRendererCollection;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererCollectionTrait;

abstract class AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererCollectionTrait;

    public function __construct(ButtonRendererCollection $buttonRendererCollection)
    {
        $this->setButtonRendererCollection($buttonRendererCollection);
    }
}