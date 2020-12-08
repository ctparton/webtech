<?php
/**
 * A class that contains code to handle any requests for  /profile/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /profile/
 */
    class Profile extends \Framework\Siteaction
    {
/**
 * Handle profile operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            return '@content/profile.twig';
        }
    }
?>