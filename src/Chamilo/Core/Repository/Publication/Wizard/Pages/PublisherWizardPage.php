<?php
namespace Chamilo\Core\Repository\Publication\Wizard\Pages;

use Chamilo\Libraries\Format\Form\FormValidatorPage;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class PublisherWizardPage extends FormValidatorPage
{

    /**
     *
     * @var PublisherComponent
     */
    private $parent;

    /**
     *
     * @param string $name
     * @param PublisherComponent $parent
     */
    public function __construct($name, $parent)
    {
        $this->parent = $parent;
        parent :: __construct($name, 'post');
        $this->updateAttributes(
            array(
                'action' => $parent->get_url(
                    array(
                        \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID => Request :: get(
                            \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID)))));
    }

    /**
     *
     * @return PublisherComponent
     */
    public function get_parent()
    {
        return $this->parent;
    }
}
