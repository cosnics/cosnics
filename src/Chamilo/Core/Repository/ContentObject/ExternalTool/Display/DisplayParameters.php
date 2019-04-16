<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Display
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisplayParameters
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    protected $externalTool;

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool(): ExternalTool
    {
        return $this->externalTool;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool $externalTool
     */
    public function setExternalTool(ExternalTool $externalTool): void
    {
        $this->externalTool = $externalTool;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->externalTool instanceof ExternalTool;
    }
}