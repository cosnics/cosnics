<?php
namespace Chamilo\Application\Weblcms\Course\Interfaces;

/**
 * Marker interface that describes the support for course submanagers.
 * Implement this interface and the needed methods if you want to use the course
 * submanager.
 */
interface CourseSubManagerSupport
{

    /**
     * Redirects the submanager to another component after a quick create
     * 
     * @param $succes boolean
     * @param $message String
     */
    public function redirect_after_quick_create($succes, $message);

    /**
     * Redirects the submanager to another component after a quick update
     * 
     * @param boolean $succes
     * @param String $message
     */
    public function redirect_after_quick_update($succes, $message);
}
