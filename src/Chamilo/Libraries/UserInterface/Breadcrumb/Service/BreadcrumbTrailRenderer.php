<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Service;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Breadcrumb\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BreadcrumbTrailRenderer
{
    private StringUtilities $stringUtilities;

    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    public function render(BreadcrumbTrail $breadcrumbTrail): string
    {
        if ($breadcrumbTrail->count() == 0) {
            return '';
        }

        $html = [];

        $html[] = '<nav class="bg-body-tertiary" aria-label="breadcrumb">';
        $html[] = '<div class="container-xxl">';
        $html[] = $this->renderBreadcrumbs($breadcrumbTrail);
        $html[] = '</div>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function renderBreadcrumb(Breadcrumb $breadcrumb): string
    {
        $html = [];

        $html[] = '<li class="breadcrumb-item">';
        $html[] = '<a href="' . htmlentities($breadcrumb->getUrl()) . '" target="_self">';

        if ($breadcrumb->getInlineGlyph() instanceof InlineGlyph) {
            $html[] = $breadcrumb->getInlineGlyph()->render();
        }
        else {
            $html[] = $this->getStringUtilities()->truncate($breadcrumb->getName(), 50);
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode('', $html);
    }

    public function renderBreadcrumbs(BreadcrumbTrail $breadcrumbTrail): string
    {
        $html = [];

        $html[] = '<ol class="breadcrumb breadcrumb-chevron py-3">';

        foreach ($breadcrumbTrail->toArray() as $breadcrumb) {
            $html[] = $this->renderBreadcrumb($breadcrumb);
        }

        $html[] = '</ol>';

        return implode(PHP_EOL, $html);
    }
}

