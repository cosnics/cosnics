<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Authentication\QueryAuthentication;
use Chamilo\Libraries\Calendar\Renderer\Type\ICalRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupport
{
    const PARAM_DOWNLOAD = 'download';

    /**
     *
     * @var \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private $calendarRendererProvider;

    private $publications;

    public function run()
    {
        $authenticationValidator = new AuthenticationValidator(
            $this->getRequest(),
            $this->getService('chamilo.configuration.service.configuration_consulter'));

        if (! $authenticationValidator->isAuthenticated())
        {
            $authentication = QueryAuthentication::factory('SecurityToken', $this->getRequest());
            $user = $authentication->login();

            if ($user instanceof User)
            {
                $this->getApplicationConfiguration()->setUser($user);
                $this->renderCalendar();
                $authentication->logout($user);
            }
            else
            {
                $response = new \Symfony\Component\HttpFoundation\Response();
                $response->setStatusCode(401);
                $response->send();
            }
        }
        else
        {
            if ($this->getRequest()->query->get(self::PARAM_DOWNLOAD))
            {
                $this->renderCalendar();
            }
            else
            {
                $downloadParameters = $this->get_parameters();
                $downloadParameters[self::PARAM_DOWNLOAD] = 1;

                $icalDownloadUrl = new Redirect($downloadParameters);

                $externalParameters = $this->get_parameters();
                $externalParameters[User::PROPERTY_SECURITY_TOKEN] = $this->getUser()->get_security_token();

                $icalExternalUrl = new Redirect($externalParameters);

                $html = array();

                $html[] = $this->render_header();

                $html[] = Display::normal_message(
                    Translation::get('ICalExternalMessage', array('URL' => $icalExternalUrl->getUrl())));

                $html[] = Display::normal_message(
                    Translation::get('ICalDownloadMessage', array('URL' => $icalDownloadUrl->getUrl())));

                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private function getCalendarRendererProvider()
    {
        if (! isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getPublicationService(),
                $this->get_course(),
                $this->get_tool_id(),
                $this->get_user(),
                $this->get_user(),
                array());
        }

        return $this->calendarRendererProvider;
    }

    private function renderCalendar()
    {
        $icalRenderer = new ICalRenderer($this->getCalendarRendererProvider());
        $icalRenderer->renderAndSend();
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Service\PublicationService
     */
    protected function getPublicationService()
    {
        return $this->getService('chamilo.application.weblcms.service.publication');
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication[]
     */
    public function get_publications()
    {
        return $this->getPublicationService()->getPublicationsForUser(
            $this->getUser(),
            $this->get_course(),
            $this->get_tool_id());
    }
}