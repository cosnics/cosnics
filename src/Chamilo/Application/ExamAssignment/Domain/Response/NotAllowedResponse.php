<?php

namespace Chamilo\Application\ExamAssignment\Domain\Response;

use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotAllowedResponse
 *
 * @package Chamilo\Application\ExamAssignment\Domain\Response
 */
class NotAllowedResponse extends Response
{
    /**
     * NotAllowedResponse constructor.
     */
    public function __construct()
    {
        $content = [];

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        $content[] = Page::getInstance()->getHeader()->render();

        $content[] = '<div class="alert alert-danger text-center">';
        $content[] = Translation::get('NotAllowed', [], \Chamilo\Libraries\Utilities\Utilities::COMMON_LIBRARIES);
        $content[] = '</div>';
        $content[] = Page::getInstance()->getFooter()->render();

        parent::__construct(implode(PHP_EOL, $content));
    }
}
