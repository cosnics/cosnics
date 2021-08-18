<?php
namespace Chamilo\Core\Repository\ContentObject\Presence\Form;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 *
 * @package repository.lib.content_object.evaluation
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A form to create/update a presence
 */
class PresenceForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $presence = new Presence();
        $presence->setOptions($this->getTranslator()->getLocale() == 'nl' ? Presence::OPTIONS_DEFAULTS_NL : Presence::OPTIONS_DEFAULTS_EN);
        $this->set_content_object($presence);
        return parent::create_content_object();
    }
}
