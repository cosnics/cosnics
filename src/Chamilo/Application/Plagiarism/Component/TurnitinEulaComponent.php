<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Service\Turnitin\EulaService;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Response\DefaultComponentResponse;
use Chamilo\Libraries\Format\Response\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinEulaComponent extends Manager
{
    const REDIRECT_URL = 'RedirectUrl';
    const PARAM_ACCEPT_EULA = 'AcceptEULA';
    const PARAM_VIEW_ONLY = 'ViewOnly';

    /**
     * @return string|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    function run()
    {
        $viewOnly = !empty($this->getRequest()->getFromUrl(self::PARAM_VIEW_ONLY));

        if(!$viewOnly)
        {
            $redirectUrl = $this->getSessionUtilities()->get(self::REDIRECT_URL);

            if (empty($redirectUrl))
            {
                throw new \RuntimeException(
                    'The redirect URL for the user is not registered and therefor the EULA can not be accepted'
                );
            }

            if ($this->getEulaService()->userHasAcceptedEULA($this->getUser()))
            {
                return $this->redirectToUrl($redirectUrl);
            }

            $acceptEULA = $this->getRequest()->getFromUrl(self::PARAM_ACCEPT_EULA);
            if ($acceptEULA)
            {
                $this->getEulaService()->acceptEULA($this->getUser());

                return $this->redirectToURL($redirectUrl);
            }
        }

        return $this->displayEULA($viewOnly);
    }

    /**
     * @param bool $viewOnly
     *
     * @return \Chamilo\Libraries\Format\Response\Response
     * @throws \Exception
     */
    protected function displayEULA(bool $viewOnly = false)
    {
        $html = array();

        if(!$viewOnly)
        {
            $html[] = '<div class="alert alert-info">';
            $html[] = $this->getTranslator()->trans('EULAInfo', [], Manager::context());
            $html[] = '</div>';
        }

        $html[] = '<div class="eula-info-page">';
        $html[] = $this->getEulaService()->getEULAPage();
        $html[] = '</div>';

        if(!$viewOnly)
        {
            $acceptEULAUrl = $this->get_url([self::PARAM_ACCEPT_EULA => 1]);

            $html[] = '<div class="text-center">';
            $html[] = '<a href="' . $acceptEULAUrl . '">';
            $html[] = '<button type="button" class="btn btn-primary">';
            $html[] = $this->getTranslator()->trans('AcceptEULA', [], Manager::context());
            $html[] = '</button>';
            $html[] = '</a>';
            $html[] = '</div>';
        }

        $content = implode(PHP_EOL, $html);

        return new DefaultComponentResponse($this, $content);
    }

    /**
     * @param string $redirectURL
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToURL(string $redirectURL)
    {
        $this->getSessionUtilities()->unregister(self::REDIRECT_URL);
        return new RedirectResponse($redirectURL);
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\Turnitin\EulaService
     */
    protected function getEulaService()
    {
        return $this->getService(EulaService::class);
    }
}