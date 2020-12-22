<?php
/**
 * A class that contains code to handle any requests for /projects/
 * This class passes the list of all project beans to the projects twig. 
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
            // setCache($context);
            $user =  $context->user();
            $projects = \R::findAll('project');
            $context->local()->addval('projects', $projects);
            $context->local()->addval('user', $user);
            return '@content/projects.twig';
        }
    }
?>