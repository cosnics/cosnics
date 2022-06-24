<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Libraries\Format\Tabs\Actions;

interface ActionProviderInterface
{

    public function getActions(): Actions;

    public function getContext(): string;
}