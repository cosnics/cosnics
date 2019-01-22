<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
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

        return $this->renderBrowser($team);
    }

    /**
     * @param Team $team
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderBrowser(Team $team): string
    {
        return $this->render(
            [
                'TEAM_URL' => $team->getWebUrl()
            ],
            'Chamilo\Application\Weblcms\Tool\Implementation\Teams:Browser.html.twig'
        );
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderCreateTeam(): string
    {
        $createTeamUrlParameters = $this->get_parameters();
        $createTeamUrlParameters[self::PARAM_ACTION] = self::ACTION_CREATE_TEAM;

        return $this->render(
            [
                'CREATE_TEAM_URL' => $this->get_url($createTeamUrlParameters),
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