<?php
/**
 * A class that contains code to handle any requests for  /search/
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \R;
/**
 * Support /search/
 */
    class Search extends \Framework\Siteaction
    {
/**
 * Handle search operations by project title. Will return any project
 * where the name begins with the search query
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            return '@content/search.twig';
        }
    }
?>