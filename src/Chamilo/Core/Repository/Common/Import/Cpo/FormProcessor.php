<?php
namespace Chamilo\Core\Repository\Common\Import\Cpo;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import\Cpo
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
        return new CpoImportParameters(
            'cpo', 
            $this->getUserIdentifier(), 
            $this->getWorkspace(), 
            $this->determineCategoryIdentifier(), 
            $this->getFile(), 
            $this->getFormValues());
    }
}