<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Component;

use Chamilo\Application\Weblcms\Component\CourseViewerComponent;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Authentication\QueryAuthentication;
use Chamilo\Libraries\Calendar\Renderer\Type\ICalRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Translation\Translation;

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

    /**
     * ICalComponent constructor.
     *
     * @param ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        Application::__construct($applicationConfiguration);
    }

    /**
     * @return string
     *
     * @throws NotAllowedException
     */
    public function run()
    {
        $authenticationValidator = new AuthenticationValidator(
            $this->getRequest(),
            $this->getService('chamilo.configuration.service.configuration_consulter')
        );

        $alreadyAuthenticated = $authenticationValidator->isAuthenticated();

        $securityCode = $this->getRequest()->get(User::PROPERTY_SECURITY_TOKEN);
        if (isset($securityCode))
        {
            $authentication = QueryAuthentication::factory('SecurityToken', $this->getRequest());
            $user = $authentication->login();

            if ($user instanceof User)
            {
                $this->renderCalendar($user);
                if(!$alreadyAuthenticated)
                {
                    $authentication->logout($user);
                }
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
                $this->renderCalendar($this->getUser());
            }
            else
            {
                if (!$this->get_application() instanceof CourseViewerComponent)
                {
                    throw new NotAllowedException();
                }

                $downloadParameters = $this->get_parameters();
                $downloadParameters[self::PARAM_DOWNLOAD] = 1;

                $icalDownloadUrl = new Redirect($downloadParameters);

                $externalParameters = $this->get_parameters();
                $externalParameters[Application::PARAM_CONTEXT] =
                    'Chamilo\Application\Weblcms\Tool\Implementation\Calendar';
                $externalParameters[User::PROPERTY_SECURITY_TOKEN] = $this->getUser()->get_security_token();

                $icalExternalUrl = new Redirect(
                    $externalParameters, [Application::PARAM_ACTION, \Chamilo\Application\Weblcms\Manager::PARAM_TOOL]
                );

                $html = array();

                $html[] = $this->render_header();

                $html[] = Display::normal_message(
                    Translation::get('ICalExternalMessage', array('URL' => $icalExternalUrl->getUrl()))
                );

                $html[] = Display::normal_message(
                    Translation::get('ICalDownloadMessage', array('URL' => $icalDownloadUrl->getUrl()))
                );

                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     *
     * @param User $user
     *
     * @return \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private function getCalendarRendererProvider(User $user)
    {
        if (!isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getPublicationService(),
                $this->get_course(),
                $this->get_tool_id(),
                $user,
                $user,
                array()
            );
        }

        return $this->calendarRendererProvider;
    }

    private function renderCalendar(User $user)
    {
        $icalRenderer = new ICalRenderer($this->getCalendarRendererProvider($user));
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
            $this->get_tool_id()
        );
    }

    public function get_course()
    {
        $course = new Course();
        $course->setId($this->getRequest()->get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE));

        return $course;
    }

    public function get_tool_id()
    {
        return 'Calendar';
    }
}