<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

abstract class Manager extends Application implements BreadcrumbLessComponentInterface
{
    public const CONTEXT = __NAMESPACE__;
}
