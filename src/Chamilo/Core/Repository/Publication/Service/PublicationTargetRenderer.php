<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRenderer
{

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function addFooterToForm(FormValidator $form)
    {
        $tableFooter = array();

        $tableFooter[] = '</tbody>';
        $tableFooter[] = '</table>';

        $form->addElement('category');
        $form->addElement('html', implode(PHP_EOL, $tableFooter));
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param string[] $columnNames
     * @param boolean $hasOnlyOneLocation
     */
    public function addHeaderToForm(FormValidator $form, string $title, array $columnNames, $hasOnlyOneLocation = false)
    {
        $tableHeader = array();

        $tableHeader[] = '<table class="table table-striped table-bordered table-hover table-responsive">';
        $tableHeader[] = '<thead>';
        $tableHeader[] = '<tr>';

        $tableHeader[] = '<th class="cell-stat-x2">';

        if (!$hasOnlyOneLocation)
        {
            $tableHeader[] = '<div class="checkbox no-toggle-style">';
            $tableHeader[] = '<input class="select-all" type="checkbox" />';
            $tableHeader[] = '<label></label>';
            $tableHeader[] = '</div>';
        }

        $tableHeader[] = '</th>';

        foreach ($columnNames as $columnName)
        {
            $tableHeader[] = '<th>' . $columnName . '</th>';
        }

        $tableHeader[] = '</th>';

        $tableHeader[] = '</tr>';
        $tableHeader[] = '</thead>';
        $tableHeader[] = '<tbody>';

        $form->addElement('category', $title, 'publication-location');
        $form->addElement('html', implode(PHP_EOL, $tableHeader));
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param string $publicationContext
     * @param string $targetKey
     * @param string[] $targetNames
     */
    public function addPublicationTargetToForm(
        FormValidator $form, string $publicationContext, string $targetKey, array $targetNames
    )
    {
        $renderer = $form->defaultRenderer();

        $group = array();

        $group[] = $form->createElement('checkbox', $this->getCheckboxName($publicationContext, $targetKey));

        foreach ($targetNames as $targetName)
        {
            $group[] = $form->createElement('static', null, null, $targetName);
        }

        $form->addGroup($group, 'target_' . $targetKey, null, '', false);

        $renderer->setElementTemplate('<tr>{element}</tr>', 'target_' . $targetKey);
        $renderer->setGroupElementTemplate('<td>{element}</td>', 'target_' . $targetKey);
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param string $publicationContext
     * @param string $targetKey
     * @param string $targetName
     */
    public function addSinglePublicationTargetToForm(
        FormValidator $form, string $publicationContext, string $targetKey, string $targetName
    )
    {
        $columnName = $this->getTranslator()->trans('Target', [], Manager::context());

        $this->addHeaderToForm($form, $targetName, [$columnName], true);
        $this->addPublicationTargetToForm($form, $publicationContext, $targetKey, [$targetName]);
        $this->addFooterToForm($form);
    }

    /**
     * @param string $publicationContext
     * @param string $targetKey
     *
     * @return string
     */
    protected function getCheckboxName(string $publicationContext, string $targetKey)
    {
        return Manager::WIZARD_TARGET . '[' . $publicationContext . '][' . Manager::WIZARD_TARGET . '][' . $targetKey .
            ']';
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}