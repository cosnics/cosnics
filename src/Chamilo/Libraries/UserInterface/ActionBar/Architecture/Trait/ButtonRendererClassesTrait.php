<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererClassesTrait
{
    public function renderClasses(ButtonInterface $button, array $baseClassesBefore = [], array $baseClassesAfter = []
    ): string
    {
        return implode(' ', array_merge($baseClassesBefore, $button->getClasses(), $baseClassesAfter));
    }
}