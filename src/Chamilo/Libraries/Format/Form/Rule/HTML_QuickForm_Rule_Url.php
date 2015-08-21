<?php
/**
 *
 * @package common.html.formvalidator.Rule
 */
/**
 * QuickForm rule to check if a url is of the correct format
 */
class HTML_QuickForm_Rule_Url extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a url is of the correct format
     * 
     * @see HTML_QuickForm_Rule
     * @param string $url Wanted url
     * @return boolean True if url is of the correct format
     */
    public function validate($url)
    {
        
        // EDIT 13/07/2011 (Stijn Van Hoecke - Hogent)
        // -------------------------------------------
        // due to new standards the entire check became invalid:
        // - no support for capitals
        // - no support for special chars (like ~)
        // - no support for newer domain names (like .info)
        // - new standard sopports full UTF8 url (such as chinese chars)
        //
        // conclusion: only the protocol check remains valid...
        $regex = '(https?|ftp)\:\/\/'; // SCHEME
                                       // $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User
                                       // and Pass
                                       // $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
                                       // $regex .= "(\:[0-9]{2,5})?"; // Port
                                       // $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
                                       // $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
                                       // $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
        
        $result = preg_match("/^$regex/i", $url);
        return $result;
    }
}
