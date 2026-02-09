<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Response;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExceptionResponse extends Response
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \Exception
     */
    public function __construct(Exception $exception)
    {
        $html = [];

        $html[] = $this->getHeaderRenderer()->render();
        $html[] = $this->getNotificationMessageRenderer()->renderOne(
            new NotificationMessage($exception->getMessage(), NotificationMessage::TYPE_DANGER), false
        );
        $html[] = $this->getFooterRenderer()->render();

        parent::__construct(implode(PHP_EOL, $html));
    }

    protected function getFooterRenderer(): BaseFooterRenderer
    {
        return $this->getService(BaseFooterRenderer::class);
    }

    protected function getHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->getService(BaseHeaderRenderer::class);
    }
}