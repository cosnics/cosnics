<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererClassesTrait
{
    /**
     * @return string[]
     */
    public function getDefaultButtonClasses(array $additionalClasses = []): array
    {
        return array_merge(['btn'], $additionalClasses);
    }

    public function renderClasses(ButtonInterface $button, array $baseClassesBefore = [], array $baseClassesAfter = []
    ): string
    {
        return implode(' ', array_merge($baseClassesBefore, $button->getClasses(), $baseClassesAfter));
    }
}