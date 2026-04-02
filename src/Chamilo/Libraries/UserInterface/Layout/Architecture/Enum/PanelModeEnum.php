<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Enum;

enum PanelModeEnum: string
{
    case DANGER = 'danger-subtle';
    case DEFAULT = 'light-subtle';
    case INFO = 'info-subtle';
    case PRIMARY = 'primary-subtle';
    case SUCCESS = 'success-subtle';
    case WARNING = 'warning-subtle';
}
