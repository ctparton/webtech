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
            $projects = \R::findAll('project');
            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                throw new \Framework\Exception\InternalError('No user');
            }
            // filter out projects that user does not own or contribute to
            $projects = array_filter($projects, function($e) use (&$user){ return in_array($user->login, array_map(function($e){ return $e->login; } , $e->sharedUserList)); });

            // Send all projects and the current user to twig
            $context->local()->addval('projects', $projects);
            $context->local()->addval('user', $user);
            return '@content/projects.twig';
        }
    }
?>