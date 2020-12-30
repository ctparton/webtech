<?php
/**
 * A class that contains code to handle any requests for  /profile/
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \R;
/**
 * Support /profile/
 */
    class Profile extends \Framework\Siteaction
    {
        use \Support\NoCache;        
/**
 * Handle profile operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $user =  $context->user();
            $projects  = R::find( 'project', ' user_id = ? ', [ $user->id ] );
            $context->local()->addval('projects', $projects);
            $context->local()->addval('user', $user);
            return '@content/profile.twig';
        }
    }
?>