<?php
namespace Chamilo\Application\Weblcms\Service\UserDetails;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Traits\UserDetailsRendererTrait;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Service\UserDetails
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    protected UrlGenerator $urlGenerator;

    public function __construct(Translator $translator, UrlGenerator $urlGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function getGlyph(): InlineGlyph
    {
        return new NamespaceIdentGlyph(Manager::CONTEXT, true);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function hasContentForUser(User $user, User $requestingUser): bool
    {
        $courses = DataManager::retrieve_all_courses_from_user($user);

        return $courses->count() > 0;
    }

    public function renderTitle(User $user, User $requestingUser): string
    {
        return $this->getTranslator()->trans('TypeName', [], \Chamilo\Core\Group\Manager::CONTEXT);
    }

    /**
     * @throws \TableException
     */
    public function renderUserDetails(User $user, User $requestingUser): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $table->setHeaderContents(0, 0, $translator->trans('Courses', [], Manager::CONTEXT));
        $table->setCellAttributes(0, 0, ['colspan' => 2, 'style' => 'text-align: center;']);

        $table->setHeaderContents(1, 0, $translator->trans('CourseCode', [], Manager::CONTEXT));
        $table->setHeaderContents(1, 1, $translator->trans('CourseName', [], Manager::CONTEXT));

        $courses = DataManager::retrieve_all_courses_from_user($user);

        if ($courses->count() == 0)
        {
            $table->setCellContents(2, 0, $translator->trans('NoCourses', [], Manager::CONTEXT));
            $table->setCellAttributes(2, 0, ['colspan' => 2, 'style' => 'text-align: center;']);
        }

        $index = 2;

        $urlGenerator = $this->getUrlGenerator();

        foreach ($courses as $course)
        {
            $url = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_VIEW_COURSE,
                    Manager::PARAM_COURSE => $course->getId()
                ]
            );

            $url = '<a href="' . $url . '">';
            $table->setCellContents($index, 0, $url . $course->get_visual_code() . '</a>');
            $table->setCellAttributes($index, 0, ['style' => 'width: 150px;']);
            $table->setCellContents($index, 1, $url . $course->get_title() . '</a>');
            $index ++;
        }

        $html[] = $table->toHtml();

        return implode(PHP_EOL, $html);
    }
}