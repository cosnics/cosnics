<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\PlatformGroupTeamService;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Model\Team;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class BrowserComponent
 */
class BrowserComponent extends Manager
{

    use ContainerAwareTrait;

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws GraphException
     * @throws \Exception
     */
    public function run(): string
    {
        $courseTeamService = $this->getCourseTeamService();
        $hasTeam = $courseTeamService->courseHasTeam($this->get_course());
        $courseTeamName = null;

        if($hasTeam)
        {
            $courseTeam = $courseTeamService->getTeam($this->get_course());
            $courseTeamName = $courseTeam->getProperties()['displayName'];
        }

        return $this->render(
            [
                'COURSE_HAS_TEAM' => $hasTeam,
                'COURSE_TEAM_NAME' => $courseTeamName,
                'IS_TEACHER' => $this->get_course()->is_course_admin($this->getUser()),
                'TEAM_URL' => $this->getUrlWithAction(self::ACTION_GO_TO_TEAM),
                'REMOVE_TEAM_USERS_URL' => $this->getUrlWithAction(self::ACTION_REMOVE_TEAM_USERS_NOT_IN_COURSE),
                'SUBSCRIBE_ALL_COURSE_USERS_URL' => $this->getUrlWithAction(
                    self::ACTION_SUBSCRIBE_ALL_COURSE_USERS_TO_TEAM
                ),
                'CREATE_TEAM_URL' => $this->getUrlWithAction(self::ACTION_CREATE_TEAM),
                'CREATE_PLATFORM_GROUP_TEAM_URL' => $this->getUrlWithAction(self::ACTION_CREATE_PLATFORM_GROUP_TEAM),
                'SUBSCRIBE_PLATFORM_GROUP_TEAM_USERS_URL' => $this->getUrlWithAction(
                    self::ACTION_SUBSCRIBE_PLATFORM_GROUP_TEAM_USERS,
                    [self::PARAM_PLATFORM_GROUP_TEAM_ID => '__PLATFORM_GROUP_TEAM_ID__']
                ),
                'REMOVE_TEAM_USERS_NOT_IN_GROUPS_URL' => $this->getUrlWithAction(
                    self::ACTION_REMOVE_TEAM_USERS_NOT_IN_GROUPS,
                    [self::PARAM_PLATFORM_GROUP_TEAM_ID => '__PLATFORM_GROUP_TEAM_ID__']
                ),
                'VISIT_PLATFORM_GROUP_TEAM_URL' => $this->getUrlWithAction(
                    self::ACTION_VISIT_PLATFORM_GROUP_TEAM,
                    [self::PARAM_PLATFORM_GROUP_TEAM_ID => '__PLATFORM_GROUP_TEAM_ID__']
                ),
                'PLATFORM_GROUP_TEAMS_JSON' => $this->getSerializer()->serialize(
                    $this->getPlatformGroupTeamService()->getPlatformGroupTeamsForCourse($this->get_course()), 'json'
                )
            ],
            'Chamilo\Application\Weblcms\Tool\Implementation\Teams:Browser.html.twig'
        );
    }

    /**
     * @param string $action
     * @param array $extraParameters
     *
     * @return string
     */
    protected function getUrlWithAction(string $action, $extraParameters = [])
    {
        $parameters = $this->get_parameters();
        $parameters[self::PARAM_ACTION] = $action;

        $parameters = array_merge($parameters, $extraParameters);

        return $this->get_url($parameters);
    }

    /**
     * @param array $parameters
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function render(array $parameters, string $template): string
    {
        return $this->getTwig()->render(
            $template,
            array_merge(
                [
                    'HEADER' => $this->render_header(''),
                    'FOOTER' => $this->render_footer(),
                ],
                $parameters
            )
        );
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

}