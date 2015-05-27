<?php
namespace Chamilo\Core\Repository\Selector;

/**
 *
 * @author Hans De Bisschop
 */
interface TabsTypeSelectorSupport
{

    /**
     *
     * @param int $template_registration_id
     * @return string
     */
    public function get_content_object_type_creation_url($template_registration_id);

    /**
     *
     * @return int
     */
    public function get_user_id();
}
