<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Component;

use Chamilo\Application\Weblcms\Component\CourseViewerComponent;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Authentication\SecurityToken\SecurityTokenAuthentication;
use Chamilo\Libraries\Calendar\Service\View\ICalCalendarRenderer;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupport
{
    public const PARAM_DOWNLOAD = 'download';

    /**
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
     * @throws NotAllowedException
     */
    public function run()
    {
        $authenticationValidator = $this->getAuthenticationValidator();

        $alreadyAuthenticated = $authenticationValidator->isAuthenticated();

        $securityCode = $this->getRequest()->getFromRequestOrQuery(User::PROPERTY_SECURITY_TOKEN);
        if (isset($securityCode))
        {
            $authentication = $this->getSecurityTokenAuthentication();
            $authentication->disableAuthSourceCheck();
            $user = $authentication->login();

            if ($user instanceof User)
            {
                $this->renderCalendar($user);
                if (!$alreadyAuthenticated)
                {
                    $authentication->logout($user);
                }
            }
            else
            {
                $response = new Response();
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

                $icalDownloadUrl = $this->getUrlGenerator()->fromParameters($downloadParameters);

                $externalParameters = $this->get_parameters();
                $externalParameters[Application::PARAM_CONTEXT] =
                    'Chamilo\Application\Weblcms\Tool\Implementation\Calendar';
                $externalParameters[User::PROPERTY_SECURITY_TOKEN] = $this->getUser()->get_security_token();

                $icalExternalUrl = $this->getUrlGenerator()->fromParameters(
                    $externalParameters, [Application::PARAM_ACTION, \Chamilo\Application\Weblcms\Manager::PARAM_TOOL]
                );

                $html = [];

                $html[] = $this->render_header();

                $html[] = Display::normal_message(
                    Translation::get('ICalExternalMessage', ['URL' => $icalExternalUrl])
                );

                $html[] = Display::normal_message(
                    Translation::get('ICalDownloadMessage', ['URL' => $icalDownloadUrl])
                );

                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     * @return AuthenticationValidator
     */
    protected function getAuthenticationValidator()
    {
        return $this->getService(AuthenticationValidator::class);
    }

    /**
     * @param User $user
     *
     * @return \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    private function getCalendarRendererProvider(User $user)
    {
        if (!isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getPublicationService(), $this->get_course(), $this->get_tool_id(), $user, []
            );
        }

        return $this->calendarRendererProvider;
    }

    public function getICalCalendarRenderer(): ICalCalendarRenderer
    {
        return $this->getService(ICalCalendarRenderer::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\PublicationService
     */
    protected function getPublicationService()
    {
        return $this->getService(PublicationService::class);
    }

    /**
     * @return SecurityTokenAuthentication
     */
    protected function getSecurityTokenAuthentication()
    {
        return $this->getService(SecurityTokenAuthentication::class);
    }

    public function get_course()
    {
        $course = new Course();
        $course->setId($this->getRequest()->getFromRequestOrQuery(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE));

        return $course;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication[]
     */
    public function get_publications()
    {
        return $this->getPublicationService()->getPublicationsForUser(
            $this->getUser(), $this->get_course(), $this->get_tool_id()
        );
    }

    public function get_tool_id()
    {
        return 'Calendar';
    }

    private function renderCalendar(User $user)
    {
        $this->getICalCalendarRenderer()->renderAndSend($this->getCalendarRendererProvider($user));
    }
}