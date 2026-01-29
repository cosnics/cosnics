<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonDividerRenderer
{

    public function render(SubButtonDivider $subButtonDivider): string
    {
        return '<li role="separator" class="' .
            implode(' ', array_merge(['divider'], $subButtonDivider->getClasses())) . '"></li>';
    }

    public function getButtonClass(): string
    {
        return SubButton::class;
    }
}