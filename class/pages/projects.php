<?php
/**
 * A class that contains code to handle any requests for  /project/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
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
/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $projects = \R::findAll('project');
            $context->local()->addval('projects', $projects);
            return '@content/projects.twig';
        }
    }
?>