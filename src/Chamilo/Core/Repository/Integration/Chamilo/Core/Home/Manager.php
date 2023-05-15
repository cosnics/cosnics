<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home;

class Manager
{
    public const CONTEXT = __NAMESPACE__;

    public function getBlockTypes()
    {
        return ['Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type\Displayer'];
    }
}