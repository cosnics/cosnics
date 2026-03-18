<?php
namespace Chamilo\Libraries\Architecture\Enum;

use Chamilo\Libraries\Component\CalendarPopupComponent;
use Chamilo\Libraries\Component\DeleteTemporaryFileComponent;
use Chamilo\Libraries\Component\UploadTemporaryFileComponent;
use Chamilo\Libraries\Component\UtilitiesComponent;

/**
 * @package Chamilo\Libraries\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case CALENDAR_POPUP = 'CalendarPopup';
    case DELETE_TEMPORARY_FILE = 'DeleteTemporaryFile';
    case UPLOAD_TEMPORARY_FILE = 'UploadTemporaryFile';
    case UTILITIES = 'Utilities';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            CalendarPopupComponent::class => self::CALENDAR_POPUP,
            DeleteTemporaryFileComponent::class => self::DELETE_TEMPORARY_FILE,
            UploadTemporaryFileComponent::class => self::UPLOAD_TEMPORARY_FILE,
            UtilitiesComponent::class => self::UTILITIES
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
