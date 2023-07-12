<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Search\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SearchForm extends FormValidator
{
    /**
     * Name of the search form
     */
    public const FORM_NAME = 'search';

    /**
     * #@+ Search parameter
     */
    public const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    /**
     * Creates a new search form
     *
     * @param string $url The location to which the search request should be posted.
     */
    public function __construct($url)
    {
        parent::__construct(self::FORM_NAME, self::FORM_METHOD_POST, $url);

        $query = $this->getQuery();

        if ($query)
        {
            $this->setDefaults([self::PARAM_SIMPLE_SEARCH_QUERY => $query]);
        }

        $this->buildForm();
    }

    /**
     * Build the simple search form.
     */
    private function buildForm()
    {
        $this->addElement(
            'text', self::PARAM_SIMPLE_SEARCH_QUERY, Translation::get('SearchFor'), ['class' => 'form-control']
        );

        $this->addElement(
            'style_button', 'submit', Translation::get('Search'), ['class' => 'btn-primary'], 'submit',
            new FontAwesomeGlyph('search')
        );

        $renderer = $this->get_renderer();
        $renderer->setElementTemplate(
            '<div class="form-group"><label>{label}</label>{element}</div>', self::PARAM_SIMPLE_SEARCH_QUERY
        );
        $renderer->setElementTemplate('{element}', 'submit');
    }

    public function clearFormSubmitted()
    {
        return !is_null($this->getRequest()->request->get('clear'));
    }

    /**
     * Gets the conditions that this form introduces.
     *
     * @return String the query
     */
    public function getQuery()
    {
        $query = $this->getRequest()->request->get(self::PARAM_SIMPLE_SEARCH_QUERY);

        if (!$query)
        {
            $query = $this->getRequest()->query->get(self::PARAM_SIMPLE_SEARCH_QUERY);
        }

        return $query;
    }
}
