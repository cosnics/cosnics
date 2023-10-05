<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserExportEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserImportEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserDeleteEvent;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserLogoutEvent;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserEventListenerInterface
{
    public function afterCreate(AfterUserCreateEvent $afterUserCreateEvent): bool;

    public function afterDelete(AfterUserDeleteEvent $afterUserDeleteEvent): bool;

    public function afterEnterPage(AfterUserEnterPageEvent $afterUserEnterPage): bool;

    public function afterExport(AfterUserExportEvent $afterUserExportEvent): bool;

    public function afterImport(AfterUserImportEvent $afterUserImportEvent): bool;

    public function afterLogin(AfterUserLoginEvent $afterUserLoginEvent): bool;

    public function afterPasswordReset(AfterUserPasswordResetEvent $afterUserPasswordResetEvent): bool;

    public function afterRegistration(AfterUserRegistrationEvent $afterUserRegistrationEvent): bool;

    public function afterUpdate(AfterUserUpdateEvent $afterUserUpdateEvent): bool;

    public function beforeDelete(BeforeUserDeleteEvent $beforeUserDeleteEvent): bool;

    public function beforeLeavePage(BeforeUserLeavePageEvent $beforeUserLeavePage): bool;

    public function beforeLogout(BeforeUserLogoutEvent $beforeUserLogoutEvent): bool;
}