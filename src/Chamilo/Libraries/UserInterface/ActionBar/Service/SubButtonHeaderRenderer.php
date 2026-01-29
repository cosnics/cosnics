<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader;

/**
 *
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonHeaderRenderer
{

    public function render(SubButtonHeader $button): string
    {
        $html = [];

        $html[] = '<li class="' . implode(' ', array_merge(['dropdown-header'], $button->getClasses())) . '">';
        $html[] = $button->getLabel();
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return SubButtonHeader::class;
    }
}