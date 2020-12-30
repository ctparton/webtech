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
 * Support /profile/, the profile page contains projects that the user owns
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
            $context->web()->addCSP('script-src', 'd3js.org');
            $user =  $context->user();
            // $projects  = R::find( 'project', ' user_id = ? ', [ $user->id ] );
            $projects = \R::findAll('project');
            $pTime = array();
            foreach ($projects as $p) 
            {
                $project = $context->load('project', (int) $p->id, TRUE);
                $timeSpent = R::getCell('SELECT SUM(duration) FROM note WHERE project_id = :pid AND user_id = :uid',[':pid' => $project->id, 'uid' => $user->id]);
                if (is_null($timeSpent)) 
                {
                    array_push($pTime, (object) array("name" => $project->name, "time" => 0));
                } 
                else 
                {
                    array_push($pTime, (object) array("name" => $project->name, "time" => $timeSpent / 86400));
                } 
            }
            $context->local()->addval('pTime', $pTime);
            $context->local()->addval('projects', $projects);
            $context->local()->addval('user', $user);
            return '@content/profile.twig';
        }
    }
?>