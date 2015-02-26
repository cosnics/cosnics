<?php
namespace Chamilo\Application\CasUser;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const APPLICATION_NAME = 'cas_user';
    const PARAM_REQUEST_ID = 'request_id';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_EDIT = 'editor';
    const ACTION_CREATE = 'creator';
    const ACTION_ACCEPT = 'accepter';
    const ACTION_REJECT = 'rejecter';
    const ACTION_ACCOUNT = 'account';
    const ACTION_SERVICE = 'service';
    const ACTION_RIGHTS = 'rights';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    public function get_update_account_request_url($account_request)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_REQUEST_ID => $account_request->get_id()));
    }

    public function get_delete_account_request_url($account_request)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_REQUEST_ID => $account_request->get_id()));
    }

    public function get_accept_account_request_url($account_request)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_ACCEPT, self :: PARAM_REQUEST_ID => $account_request->get_id()));
    }

    public function get_reject_account_request_url($account_request)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_REJECT, self :: PARAM_REQUEST_ID => $account_request->get_id()));
    }
}
