<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererRegistry;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererCollectionTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererCollectionTrait;

    public function __construct(ButtonRendererRegistry $buttonRendererCollection)
    {
        $this->setButtonRendererCollection($buttonRendererCollection);
    }
}