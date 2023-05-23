<?php
namespace Chamilo\Core\Repository\Quota;

use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;

/**
 * @package Chamilo\Core\Repository\Quota
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_DENY = 'Denier';
    public const ACTION_GRANT = 'Granter';
    public const ACTION_RIGHTS = 'Rights';
    public const ACTION_UPGRADE = 'Upgrader';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'quota_action';
    public const PARAM_REQUEST_ID = 'request_id';
    public const PARAM_RESET_CACHE = 'reset_cache';

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService(RightsService::class);
    }
}
