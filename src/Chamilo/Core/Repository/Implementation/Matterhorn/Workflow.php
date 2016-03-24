<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

class Workflow
{

    private $id;

    private $state;

    private $template;

    private $title;

    private $description;

    private $mediapackage;

    private $operations;

    private $configurations;

    private $errors;
    const PROPERTY_STATE = 'state';
    const PROPERTY_ID = 'id';

    /**
     *
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @return the $title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @return the $operations
     */
    public function get_operations()
    {
        return $this->operations;
    }

    /**
     *
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @param $operations the $operations to set
     */
    /**
     *
     * @return the $state
     */
    public function get_state()
    {
        return $this->state;
    }

    /**
     *
     * @return the $template
     */
    public function get_template()
    {
        return $this->template;
    }

    /**
     *
     * @return the $mediapackage
     */
    public function get_mediapackage()
    {
        return $this->mediapackage;
    }

    /**
     *
     * @return the $configurations
     */
    public function get_configurations()
    {
        return $this->configurations;
    }

    /**
     *
     * @return the $errors
     */
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     *
     * @param $state the $state to set
     */
    public function set_state($state)
    {
        $this->state = $state;
    }

    /**
     *
     * @param $template the $template to set
     */
    public function set_template($template)
    {
        $this->template = $template;
    }

    /**
     *
     * @param $mediapackage the $mediapackage to set
     */
    public function set_mediapackage($mediapackage)
    {
        $this->mediapackage = $mediapackage;
    }

    /**
     *
     * @param $configurations the $configurations to set
     */
    public function set_configurations($configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     *
     * @param $errors the $errors to set
     */
    public function set_errors($errors)
    {
        $this->errors = $errors;
    }

    public function set_operations($operations)
    {
        $this->operations = $operations;
    }

    public function is_published()
    {
        $operations = $this->get_operations()->operation;
        
        foreach ($operations as $operation)
        {
            if ($operation->id == 'publish' && $operation->state == 'SUCCEEDED')
            {
                return true;
            }
        }
        return false;
    }
}
