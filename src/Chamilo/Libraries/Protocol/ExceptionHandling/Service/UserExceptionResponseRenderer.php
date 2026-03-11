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
    protected DefaultFooterRenderer $defaultFooterRenderer;

    protected DefaultHeaderRenderer $defaultHeaderRenderer;

    protected UserExceptionRendererRegistry $userExceptionRendererRegistry;

    public function __construct(
        DefaultFooterRenderer $defaultFooterRenderer, DefaultHeaderRenderer $defaultHeaderRenderer,
        UserExceptionRendererRegistry $userExceptionRendererRegistry
    )
    {
        $this->defaultFooterRenderer = $defaultFooterRenderer;
        $this->defaultHeaderRenderer = $defaultHeaderRenderer;
        $this->userExceptionRendererRegistry = $userExceptionRendererRegistry;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Exception
     */
    public function render(UserExceptionInterface $exception): string
    {
        $userExceptionRenderer =
            $this->getUserExceptionRendererRegistry()->getUserExceptionRendererForUserException($exception);
        $renderedUserException = $userExceptionRenderer->render($exception);

        $html = [];

        $html[] = $this->getDefaultHeaderRenderer()->render();
        $html[] = $renderedUserException;
        $html[] = $this->getDefaultFooterRenderer()->render();

        return implode(PHP_EOL, $html);
    }

    public function getDefaultFooterRenderer(): DefaultFooterRenderer
    {
        return $this->defaultFooterRenderer;
    }

    public function getDefaultHeaderRenderer(): DefaultHeaderRenderer
    {
        return $this->defaultHeaderRenderer;
    }

    public function getUserExceptionRendererRegistry(): UserExceptionRendererRegistry
    {
        return $this->userExceptionRendererRegistry;
    }
}