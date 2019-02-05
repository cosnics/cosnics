<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
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
     */
    public function run(): string
    {
        /**
         * @var CourseTeamService $courseTeamService
         */
        $courseTeamService = $this->getService(CourseTeamService::class);

        $team = $courseTeamService->getTeam($this->get_course());
        if (is_null($team)) {
            return $this->renderCreateTeam();
        }

        return $this->renderBrowser();
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderBrowser(): string
    {
        return $this->render(
            [
                'IS_TEACHER' => $this->get_course()->is_course_admin($this->getUser()),
                'TEAM_URL' => $this->getUrlWithAction(self::ACTION_GO_TO_TEAM),
                'REMOVE_TEAM_USERS_URL' => $this->getUrlWithAction(self::ACTION_REMOVE_TEAM_USERS_NOT_IN_COURSE),
                'SUBSCRIBE_ALL_COURSE_USERS_URL' =>  $this->getUrlWithAction(self::ACTION_SUBSCRIBE_ALL_COURSE_USERS_TO_TEAM)
            ],
            'Chamilo\Application\Weblcms\Tool\Implementation\Teams:Browser.html.twig'
        );
    }

    /**
     * @param string $action
     * @return string
     */
    protected function getUrlWithAction(string $action)
    {
        $parameters = $this->get_parameters();
        $parameters[self::PARAM_ACTION] = $action;

        return $this->get_url($parameters);
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderCreateTeam(): string
    {
        return $this->render(
            [
                'CREATE_TEAM_URL' => $this->getUrlWithAction(self::ACTION_CREATE_TEAM),
                'IS_TEACHER' => $this->get_course()->is_course_admin($this->getUser())
            ],
            'Chamilo\Application\Weblcms\Tool\Implementation\Teams:CreateTeam.html.twig'
        );
    }

    /**
     * @param array $parameters
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
                    'HEADER' => $this->render_header(),
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