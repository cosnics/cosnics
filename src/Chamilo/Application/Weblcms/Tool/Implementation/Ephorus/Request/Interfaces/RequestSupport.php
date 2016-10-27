<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Interfaces;

/**
 * User: Pieterjan Broekaert
 * Date: 30/07/12
 * Time: 13:31
 */
interface RequestSupport
{

    /**
     * Returns a base request containing the author id
     * 
     * @abstract
     *
     *
     *
     *
     *
     *
     *
     * @return array
     */
    public function get_base_requests();

    /**
     * Redirects after create
     * 
     * @abstract
     *
     *
     *
     *
     *
     *
     *
     *
     * @param string $message
     * @param boolean $is_error
     */
    public function redirect_after_create($message, $is_error);

    /**
     * Returns the request guids
     * 
     * @abstract
     *
     *
     *
     *
     *
     *
     *
     * @return array
     */
    public function get_request_guids();
}
