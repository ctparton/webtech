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
 * and visualises the time the user has spent on these projects
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
            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                throw new \Framework\Exception\InternalError('No user');
            }
            $user = $context->user();
            $projects = \R::findAll('project');

            // Initialise time spent on each project
            $pTime = array();
            // filter out projects that user does not own or contribute to  
            $projects = array_filter($projects, function($e) use (&$user) {return in_array($user->login, array_map(function($e) { return $e->login; }, $e->sharedUserList) ); });

            foreach ($projects as $p) 
            {
                try
                {
                    $project = $context->load('project', (int) $p->getID(), TRUE);
                    $timeSpent = R::getCell('SELECT SUM(duration) FROM note WHERE project_id = :pid AND user_id = :uid',[':pid' => $project->getID(), 'uid' => $user->getID()]);
                    if (is_null($timeSpent)) 
                    {
                        array_push($pTime, (object) array("name" => $project->name, "time" => 0));
                    } 
                    else 
                    {
                        array_push($pTime, (object) array("name" => $project->name, "time" => $timeSpent / 86400));
                    } 
                }   
                catch (\Framework\Exception\MissingBean $e)
                {
                    $context->local()->message(\Framework\Local::ERROR, $e->getMessage().' with id '.$p->getID());
                }
                    
                
            }
            $context->local()->addval('pTime', $pTime);
            $context->local()->addval('projects', $projects);
            $context->local()->addval('user', $user);
            return '@content/profile.twig';
        }
    }
?>