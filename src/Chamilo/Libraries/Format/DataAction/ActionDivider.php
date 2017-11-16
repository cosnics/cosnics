<?php

namespace Chamilo\Libraries\Format\DataAction;

/**
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActionDivider extends AbstractAction
{
    const TYPE_NAME = 'divider';

    /**
     * ActionGroup constructor.
     */
    public function __construct()
    {
       parent::__construct('divider_' . uniqid());
    }
}