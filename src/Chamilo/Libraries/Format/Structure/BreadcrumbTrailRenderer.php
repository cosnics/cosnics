<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
        if ($breadcrumbTrail->size() == 0)
        {
            return '';
        }

        $html = [];

        $html[] = '<div class="container-breadcrumb">';
        $html[] = '<div class="' . $breadcrumbTrail->getContainerMode() . '">';
        $html[] = $this->renderBreadcrumbs($breadcrumbTrail);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    public function renderBreadcrumb(Breadcrumb $breadcrumb): string
    {
        $html = [];

        $html[] = '<li>';
        $html[] = '<a href="' . htmlentities($breadcrumb->getUrl()) . '" target="_self">';

        if ($breadcrumb->getInlineGlyph() instanceof InlineGlyph)
        {
            $html[] = $breadcrumb->getInlineGlyph()->render();
        }
        else
        {
            $html[] = $this->getStringUtilities()->truncate($breadcrumb->getName(), 50);
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode('', $html);
    }

    public function renderBreadcrumbs(BreadcrumbTrail $breadcrumbTrail): string
    {
        $html = [];

        $html[] = '<ol class="breadcrumb">';

        foreach ($breadcrumbTrail->getBreadcrumbs() as $breadcrumb)
        {
            $html[] = $this->renderBreadcrumb($breadcrumb);
        }

        $html[] = '</ol>';

        return implode(PHP_EOL, $html);
    }
}

