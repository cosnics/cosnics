<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererCollectionTrait;

abstract class AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererCollectionTrait;

    public function __construct(ButtonRendererCollection $buttonRendererCollection)
    {
        $this->setButtonRendererCollection($buttonRendererCollection);
    }
}