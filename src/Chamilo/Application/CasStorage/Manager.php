<?php
namespace Chamilo\Application\CasStorage;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\CasStorage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    const PARAM_REQUEST_ID = 'request_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_EDIT = 'Editor';
    const ACTION_CREATE = 'Creator';
    const ACTION_ACCEPT = 'Accepter';
    const ACTION_REJECT = 'Rejecter';
    const ACTION_ACCOUNT = 'Account';
    const ACTION_SERVICE = 'Service';
    const ACTION_RIGHTS = 'Rights';
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
