<?php
namespace Chamilo\Core\Repository\Common\Import\File;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FormProcessor extends \Chamilo\Core\Repository\Common\Import\FormProcessor
{

    /**
     *
     * @see \Chamilo\Core\Repository\Common\Import\FormProcessor::getImportParameters()
     */
    public function getImportParameters()
    {
        return new FileImportParameters(
            'file',
            $this->getUserIdentifier(),
            $this->getWorkspace(),
            $this->determineCategoryIdentifier(),
            $this->getFile(),
            $this->getFormValues());
    }
}