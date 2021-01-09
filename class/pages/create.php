<?php
/**
 * A class that contains code to handle any requests for /create/
 * It will allow a signed in user to create new projects
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \R;
/**
 * Support /create/
 */
    class Create extends \Framework\Siteaction
    {
/**
 * Handle create operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            return '@content/create.twig';    
        } 
    }
?>