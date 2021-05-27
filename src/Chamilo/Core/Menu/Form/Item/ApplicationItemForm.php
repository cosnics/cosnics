<?php
namespace Chamilo\Core\Menu\Form\Item;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Form\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItemForm extends ItemForm
{

    public function buildForm()
    {
        parent:: buildForm();
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Properties', [], 'Chamilo\Core\Menu'));
        $this->addElement(
            'checkbox', ApplicationItem::PROPERTY_USE_TRANSLATION,
            $translator->trans('UseTranslation', [], 'Chamilo\Core\Menu')
        );

        $this->addElement(
            'select', ApplicationItem::PROPERTY_APPLICATION, $translator->trans('Application', [], 'Chamilo\Core\Menu'),
            $this->getApplicationOptions(), array('class' => 'form-control')
        );

        $this->addRule(
            ApplicationItem::PROPERTY_APPLICATION,
            $translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES), 'required'
        );

        $this->add_textfield(
            ApplicationItem::PROPERTY_COMPONENT, $translator->trans('Component', [], 'Chamilo\Core\Menu'), false
        );
        $this->add_textfield(
            ApplicationItem::PROPERTY_EXTRA_PARAMETERS, $translator->trans('ExtraParameters', [], 'Chamilo\Core\Menu'),
            false
        );
    }

    /**
     * @return string[]
     */
    public function getApplicationOptions()
    {
        $applications = $this->getRegistrationConsulter()->getRegistrationsByType(Registration::TYPE_APPLICATION);

        $activeApplications = [];

        foreach ($applications as $application)
        {
            if (!$application[Registration::PROPERTY_STATUS])
            {
                continue;
            }

            $applicationContext = $application[Registration::PROPERTY_CONTEXT];
            $applicationName = $this->getTranslator()->trans('TypeName', [], $applicationContext);

            $activeApplications[$applicationContext] =
                $applicationName == 'TypeName' ? $applicationContext : $applicationName;
        }

        return $activeApplications;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ApplicationItem $item
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function setItemDefaults(Item $item, array $itemTitles, array $defaults = [])
    {
        $defaults[ApplicationItem::PROPERTY_APPLICATION] = $item->getApplication();
        $defaults[ApplicationItem::PROPERTY_COMPONENT] = $item->getComponent();
        $defaults[ApplicationItem::PROPERTY_EXTRA_PARAMETERS] = $item->getExtraParameters();
        $defaults[ApplicationItem::PROPERTY_USE_TRANSLATION] = $item->getUseTranslation();

        parent::setItemDefaults($item, $itemTitles, $defaults);
    }
}
