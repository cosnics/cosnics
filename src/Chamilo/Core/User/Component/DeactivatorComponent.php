<?php
namespace Chamilo\Core\User\Component;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeactivatorComponent extends ActiveChangerComponent
{

    protected function getState(): int
    {
        return 0;
    }
}
