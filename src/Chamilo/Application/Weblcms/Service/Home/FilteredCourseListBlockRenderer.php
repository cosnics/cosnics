<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\FilteredCourseListRenderer;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\CourseUserCategoryService;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * This class represents a block to show the course list filtered in a given course type and optionally a given category
 *
 * @package Chamilo\Application\Weblcms\Service\Home
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class FilteredCourseListBlockRenderer extends BlockRenderer
    implements ConfigurableBlockInterface, StaticBlockTitleInterface
{
    public const CONFIGURATION_COURSE_TYPE = 'course_type';
    public const CONFIGURATION_SHOW_NEW_ICONS = 'show_new_icons';

    public const PARAM_COURSE_TYPE = 'course_type';

    protected CourseService $courseService;

    protected CourseUserCategoryService $courseUserCategoryService;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, CourseService $courseService,
        CourseUserCategoryService $courseUserCategoryService, ElementRightsService $elementRightsService
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService);

        $this->courseService = $courseService;
        $this->courseUserCategoryService = $courseUserCategoryService;
    }

    public function displayContent(Element $block, ?User $user = null): string
    {
        $renderer = $this->getCourseListRenderer($block);

        if ($block->getSetting(self::CONFIGURATION_SHOW_NEW_ICONS, true))
        {
            $renderer->show_new_publication_icons();
        }

        return $renderer->as_html();
    }

    public function getConfigurationVariables(): array
    {
        return [self::CONFIGURATION_SHOW_NEW_ICONS, self::CONFIGURATION_COURSE_TYPE];
    }

    public function getCourseListRenderer(Element $block): FilteredCourseListRenderer
    {
        return new FilteredCourseListRenderer(
            $this, '', $this->getCourseTypeId($block), $this->getUserCourseCategoryId($block),
            $this->getCourseService(), $this->getCourseUserCategoryService()
        );
    }

    protected function getCourseService(): CourseService
    {
        return $this->courseService;
    }

    protected function getCourseTypeConfiguration(Element $block): array
    {
        $courseTypeIds = json_decode($block->getSetting(self::CONFIGURATION_COURSE_TYPE));

        if (!is_array($courseTypeIds))
        {
            $courseTypeIds = [$courseTypeIds];
        }

        return $courseTypeIds;
    }

    protected function getCourseTypeId(Element $block): int
    {
        $courseTypeIds = $this->getCourseTypeConfiguration($block);

        return (int) $courseTypeIds[0];
    }

    protected function getCourseUserCategoryService(): CourseUserCategoryService
    {
        return $this->courseUserCategoryService;
    }

    /**
     * Returns the title of this block Changes the default title of the block to the title of the course type and
     * (optionally) the title of the selected user course category
     */
    public function getTitle(Element $block, ?User $user = null): string
    {
        $translator = $this->getTranslator();

        $courseTypeId = $this->getCourseTypeId($block);

        if ($courseTypeId > 0)
        {
            $courseType = CourseTypeDataManager::retrieve_by_id(CourseType::class, $courseTypeId);

            if ($courseType)
            {
                $courseTypeTitle = $courseType->get_title();
            }
            else
            {
                return $translator->trans('NoSuchCourseType');
            }
        }
        elseif ($courseTypeId)
        {
            $courseTypeTitle = $translator->trans('AllCourses');
        }
        else
        {
            $courseTypeTitle = $translator->trans('NoCourseType');
        }

        $userCourseCategoryId = $this->getUserCourseCategoryId($block);

        if ($userCourseCategoryId > 0)
        {

            $courseUserCategory = DataManager::retrieve_by_id(
                CourseUserCategory::class, $userCourseCategoryId
            );

            if ($courseUserCategory)
            {
                return $courseTypeTitle . ' - ' . $courseUserCategory->get_title();
            }
        }

        return $courseTypeTitle;
    }

    protected function getUserCourseCategoryId(Element $block): int
    {
        $courseTypeIds = $this->getCourseTypeConfiguration($block);

        return (int) $courseTypeIds[1];
    }

    public function renderContentFooter(Element $block): string
    {
        $html = [];

        $html[] = '</div>';

        if (!$block->getSetting(self::CONFIGURATION_SHOW_NEW_ICONS, true))
        {
            $courseTypeLink = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE => $this->getCourseTypeId($block)
                ]
            );

            $html[] = '<div class="panel-footer">';
            $html[] = $this->getTranslator()->trans('CheckWhatsNew', ['URL' => $courseTypeLink], Manager::CONTEXT);
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function renderContentHeader(Element $block): string
    {
        return '<div class="portal-block-content portal-block-course-list' . ($block->isVisible() ? '' : ' hidden') .
            '">';
    }
}
