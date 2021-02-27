<?php

namespace Chamilo\Application\ExamAssignment\Service\Kernel;

use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * Class NotAllowedResponseGenerator
 * @package Chamilo\Application\ExamAssignment\Service\Kernel
 */
class NotAllowedResponseGenerator
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * NotAllowedResponseGenerator constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Response
     */
    public function getNotAllowedResponse()
    {
        $content = [];

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        $content[] = Page::getInstance()->getHeader()->render();

        $content[] = '<div class="alert alert-danger text-center">';
        $content[] = $this->translator->trans('NotAllowed', [], Utilities::COMMON_LIBRARIES);
        $content[] = '</div>';
        $content[] = Page::getInstance()->getFooter()->render();

        return new Response(implode(PHP_EOL, $content));
    }
}
