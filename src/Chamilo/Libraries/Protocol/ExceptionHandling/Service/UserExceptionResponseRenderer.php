<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultHeaderRenderer;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserExceptionResponseRenderer
{
    public function __construct(
        protected DefaultFooterRenderer $defaultFooterRenderer, protected DefaultHeaderRenderer $defaultHeaderRenderer,
        protected UserExceptionRendererRegistry $userExceptionRendererRegistry
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Exception
     */
    public function render(UserExceptionInterface $exception): string
    {
        $userExceptionRenderer =
            $this->userExceptionRendererRegistry->getUserExceptionRendererForUserException($exception);
        $renderedUserException = $userExceptionRenderer->render($exception);

        $html = [];

        $html[] = $this->defaultHeaderRenderer->render();
        $html[] = $renderedUserException;
        $html[] = $this->defaultFooterRenderer->render();

        return implode(PHP_EOL, $html);
    }
}