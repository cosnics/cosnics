<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\User\Service\EmailService;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EmailerComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $result = $this->getEmailService()->execute($this->getUser(), $this->getCurrentTargetUserIdentifiers());

        if ($result === true)
        {
            return new RedirectResponse(
                $this->getUrlGenerator()->fromRequest([], [
                        \Chamilo\Application\Weblcms\Manager::PARAM_USERS
                    ])
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $result;
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function getCurrentTargetUserIdentifiers(): array
    {
        return (array) $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, []);
    }

    public function getEmailService(): EmailService
    {
        return $this->getService(EmailService::class);
    }

    public function renderHeader(string $pageTitle = ''): string
    {
        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->get_url(
                    [\Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->getCurrentTargetUserIdentifiers()]
                ), $this->getTranslator()->trans('EmailUsers', [], Manager::CONTEXT)
            )
        );

        return parent::renderHeader();
    }
}
