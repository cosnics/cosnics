<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 *
 * @package core\repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ComplexContentObjectItemForm extends FormValidator
{

    /**
     *
     * @var \core\repository\storage\data_class\ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /**
     *
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_content_object_item
     * @param string $action
     * @param string $method
     */
    public function __construct(
        ComplexContentObjectItem $complex_content_object_item, $action = null, $method = self::FORM_METHOD_POST
    )
    {
        parent::__construct('complex_content_object_item_form', $method, $action);
        $this->complex_content_object_item = $complex_content_object_item;
    }

    /**
     *
     * @param string $namespace
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_content_object_item
     * @param string $action
     * @param string $method
     *
     * @return \core\repository\form\ComplexContentObjectItemForm
     */
    public static function factory(
        $namespace, $complex_content_object_item, $action = null, $method = self::FORM_METHOD_POST
    )
    {
        if (!$complex_content_object_item->isExtended())
        {
            return null;
        }

        $classNameUtilities = ClassnameUtilities::getInstance();

        $contentObjectNamespace =
            $classNameUtilities->getNamespaceFromClassname(get_class($complex_content_object_item));
        $contentObjectNamespace = $classNameUtilities->getNamespaceParent($contentObjectNamespace, 2);
        $objectName = $classNameUtilities->getClassnameFromObject($complex_content_object_item);

        $class = $contentObjectNamespace . '\Form\\' . $objectName . 'Form';

        return new $class($complex_content_object_item, $action, $method);
    }

    /**
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_complex_content_object_item()
    {
        return $this->complex_content_object_item;
    }

    /**
     *
     * @return string[]
     */
    public function get_default_values()
    {
        return [];
    }

    /**
     *
     * @return \HTML_QuickForm_element[]
     */
    public function get_elements()
    {
        return [];
    }

    /**
     *
     * @return boolean
     */
    public function update()
    {
        return $this->get_complex_content_object_item()->update();
    }
}
