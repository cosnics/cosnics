<?php
namespace Chamilo\Libraries\UserInterface\Alert\Architecture\Enum;

enum AlertEnum: string
{
    case DANGER = 'danger';
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
}
