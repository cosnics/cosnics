<?php
namespace Chamilo\Libraries;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Component\CalendarPopupComponent;
use Chamilo\Libraries\Component\DeleteTemporaryFileComponent;
use Chamilo\Libraries\Component\HtmlEditorInstanceComponent;
use Chamilo\Libraries\Component\UploadTemporaryFileComponent;
use Chamilo\Libraries\Component\UtilitiesComponent;

/**
 * @package Chamilo\Libraries
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_CALENDAR_POPUP = 'CalendarPopup';
    public const ACTION_DELETE_TEMPORARY_FILE = 'DeleteTemporaryFile';
    public const ACTION_HTML_EDITOR_INSTANCE = 'HtmlEditorInstance';
    public const ACTION_UPLOAD_TEMPORARY_FILE = 'UploadTemporaryFile';
    public const ACTION_UTILITIES = 'Utilities';
    public const CONTEXT = __NAMESPACE__;

    public function getApplicationAction(): string
    {
        return match (static::class) {
            CalendarPopupComponent::class => self::ACTION_CALENDAR_POPUP,
            DeleteTemporaryFileComponent::class => self::ACTION_DELETE_TEMPORARY_FILE,
            HtmlEditorInstanceComponent::class => self::ACTION_HTML_EDITOR_INSTANCE,
            UploadTemporaryFileComponent::class => self::ACTION_UPLOAD_TEMPORARY_FILE,
            UtilitiesComponent::class => self::ACTION_UTILITIES
        };
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return self::ACTION_UTILITIES;
    }
}
