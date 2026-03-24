<?php
namespace Chamilo\Libraries\Architecture\Enum;

use Chamilo\Libraries\Component\DeleteTemporaryFileComponent;
use Chamilo\Libraries\Component\UploadTemporaryFileComponent;
use Chamilo\Libraries\Component\UtilitiesComponent;

/**
 * @package Chamilo\Libraries\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case DELETE_TEMPORARY_FILE = 'DeleteTemporaryFile';
    case UPLOAD_TEMPORARY_FILE = 'UploadTemporaryFile';
    case UTILITIES = 'Utilities';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
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
