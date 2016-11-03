<?php
/**
 * Created by PhpStorm.
 * User: tomgoethals
 * Date: 10/03/14
 * Time: 15:29
 */
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\Format\Form\FormValidator;

interface FilteredBlock
{

    /**
     *
     * @return FormValidator
     */
    function get_form($url);
}