<?php
/**
 * A class that contains code to handle any requests for /project/
 * This class will be used to return the notes and uploads of a single project
 * where requests are made in the form /project/projectid/
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \R;
/**
 * Support /project/
 */
    class Project extends \Framework\Siteaction
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
            $rest = $context->rest();

            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                throw new \Framework\Exception\InternalError('No user');
            }
            $context->local()->addval('user', $context->user()->login);
            // error will appear on page if project with id $rest[0] does not exist      
            $project = $context->load('project', (int) $rest[0], TRUE);
            $context->local()->addval('project', $project);
            $timeSpent = R::getCell('SELECT SUM(duration) FROM note WHERE project_id = :pid AND user_id = :uid',[':pid' => $project->getID(), 'uid' => (int) $context->user()->getID()]);
            $context->local()->addval('time', Project::fmtDuration($timeSpent));

            // If we are handling request to add a new note to current project
            if (count($rest) > 1)
            {
                $formData = $context->formdata('post');
                if ($formData->exists('notetext')) 
                {
                    try 
                    {
                        $user = $context->load('user', (int) $context->user()->getID(), TRUE); 

                        // upload a file if we have one
                        if ($context->formdata('file')->exists('filesubmit'))
                        {
                            $file = $context->formdata('file')->fileData('filesubmit'); 
                            $upl = \R::dispense('upload');
                            $fileSaved = $upl->savefile($context, $file, FALSE, $user, 0); 
                        }           
                        $noteModel = R::dispense('note');
                        if ($fileSaved)
                        {
                            $noteModel->sharedUploadList[] = $upl;
                        }          
                        // check note text is not empty
                        // check duration is number, client and server side
                        $noteModel->text = $formData->mustfetch('notetext');
                        $noteModel->duration = $formData->fetch('secondsholder');
                        $noteModel->user = $user; 
                        $noteModel->startDate = $context->utcnow();
                        $project->ownNoteList[] = $noteModel;
                        R::store( $project);
                        R::store($noteModel);
                        $context->local()->message(\Framework\Local::MESSAGE, "A new note has been created ");       
                    }
                    catch (\Framework\Exception\BadValue $e) 
                    {
                        $context->local()->message(\Framework\Local::ERROR, "A new note must contain text");
                    }
                }       
                return '@content/newnote.twig';
            }
            else 
            {
                $formData = $context->formdata('post');
                if ($formData->exists('contributor')) 
                {
                    try 
                    {
                        $contributor = $formData->mustfetch('contributor');
                        $siteinfo = \Support\SiteInfo::getinstance();
                        if(in_array($contributor, array_map(function($e){return $e->login;}, $siteinfo->users()))) 
                        {

                            $addedUser = array_filter($siteinfo->users(), function($e) use (&$contributor) {return $e->login === $contributor;});
                            foreach ($addedUser as &$value)
                            {
                                $user = $context->load('user', (int) $value->getID(), TRUE);
                            }
                            $currentContributors = $project->sharedUserList;
                            $project->sharedUserList[] = $user;
                            R::store($project);
                            $context->local()->message(\Framework\Local::MESSAGE, "Added new contributor to project");
                        }
                        else 
                        {
                            $context->local()->message(\Framework\Local::ERROR, "user does not exist");
                        }
                    }
                    catch (\Framework\Exception\BadValue $e) 
                    {
                        $context->local()->message(\Framework\Local::ERROR, "Must complete contributors field");
                    }
                }    
                return '@content/project.twig';   
            }        
        }
 /**
 * Convert duration from database into nicely formatted array to pass to twig
 *
 * @param Duration   $duration  Raw duration in seconds from db
 *
 * @return string  array
 */
        public function fmtDuration($duration) : array
        {
            $day = floor($duration / (24 * 3600)); 
            $n = ($duration % (24 * 3600)); 
            $hour = $n / 3600; 
            $n %= 3600; 
            $minutes = $n / 60 ; 
            return ['day' => $day, 'hour' => $hour, 'minute' => $minutes];
        }
    }
?>