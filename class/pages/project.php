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
    class Project extends \Framework\Siteaction
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
            $rest = $context->rest();
            $context->local()->addval('rest', $rest);
            $context->local()->addval('user', $context->user()->id);
            
            $bn = $context->load('project', (int) $rest[0], TRUE);
            $context->local()->addval('project', $bn);
            
            return '@content/project.twig';
        }
    }
?>