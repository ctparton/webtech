<?php
/**
 * A class that contains code to handle any requests for /projects/
 * This class passes the list of all project beans to the projects twig for display. 
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /project/
 */
    class Projects extends \Framework\Siteaction
    {
        use \Support\NoCache;
/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $user =  $context->user();
            
            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                throw new \Framework\Exception\InternalError('No user');
            }
 
            $context->local()->addval('user', $user);
            return '@content/projects.twig';
        }
    }
?>