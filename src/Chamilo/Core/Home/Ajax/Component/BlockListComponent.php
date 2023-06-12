<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @author     Hans De Bisschop
 * @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockListComponent extends Manager
{
    public const PROPERTY_BLOCKS = 'blocks';

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $result = new JsonAjaxResult(200);
        $result->set_property(
            self::PROPERTY_BLOCKS, $this->getHomeService()->getAvailableBlockRenderersForUser($this->getUser())
        );
        $result->display();
    }
}
