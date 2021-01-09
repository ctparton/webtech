<?php
/**
 * A class to handle any requests for /project/
 * This class will be used to return the notes and uploads of a single project
 * where requests are made in the form /project/projectid/. It will also handle creating 
 * new projects at /project/ and new notes at /project/projectid/note as well as searches
 * for projects in the form /project/?search=<query>
 * 
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
            // If we are creating a new project or returning search results 
            if ($rest[0] === '')
            {
                $formData = $context->formdata('get');
                // If we are not on search results page
                if (!$formData->exists('search'))
                {
                    $formData = $context->formdata('post');
                    // if we are POSTing to /project
                    if ($formData->exists('pname')) 
                    {
                        try 
                        {
                            $user = $context->load('user', (int) $context->user()->getID(), TRUE);
                            try 
                            {
                                $projectName = $formData->mustfetch('pname');
                                $projectDesc = $formData->mustfetch('pdesc');
        
                                // Alphanumeric with spaces is valid
                                if (!preg_match('/^[\p{L}\p{N} ]+$/', $projectName))
                                {
                                    $context->local()->message(\Framework\Local::ERROR, "Please ensure project name is alphanumeric");
                                }
                                elseif (empty($projectName) || empty($projectDesc))
                                {
                                    $context->local()->message(\Framework\Local::ERROR, "Please ensure project name and project description are not empty");
                                }
                                else 
                                {
                                    $projectModel = R::dispense('project');
                                    $projectModel->sharedUserList[] = $user;
                                    $projectModel->user = $user; 
                                    $projectModel->name = $projectName;
                                    $projectModel->description = $projectDesc;
                                    $projectModel->startDate = $context->utcnow();
                                    R::store( $projectModel);
                                    $context->local()->message(\Framework\Local::MESSAGE, "A project (".$projectName.") has been created");                                
                                }
                    
                            }
                            catch (\Framework\Exception\BadValue $e) 
                            {
                                $context->local()->message(\Framework\Local::ERROR, "Please ensure project has name and a description");
                            }
                        }
                        catch (\Framework\Exception\MissingBean $e)
                        {
                            $context->local()->message(\Framework\Local::ERROR, "Could not load user, check login conditions");
                        }           
                    } 
                    return '@content/create.twig';     
                }
                else 
                {
                    if ($formData->exists('search'))
                    {
                        $query = $formData->fetch('search');
                        $context->local()->addval('sQuery', $query );
                        if (!empty($query))
                        {
                            $projects = R::find( 'project', ' name LIKE ? ', [ $query.'%' ] );
                        } 
                        else 
                        {
                            $projects = [];
                        }
                        
                        $context->local()->addval('projects', $projects); 
                    }
                    return '@content/search.twig';
                }            
            }
            else
            {
                // error will appear on page if project with id $rest[0] does not exist      
                $project = $context->load('project', (int) $rest[0], TRUE);
                // Pass project to twig, as we may be displaying a specific project in this case
                $context->local()->addval('pid', (int) $rest[0]);
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
    
                            // if file uploaded, add to note
                            if ($fileSaved)
                            {
                                $noteModel->sharedUploadList[] = $upl;
                            }          
                            $text = $formData->mustfetch('notetext');
                            $duration = $formData->fetch('secondsholder');
                     
                            if (!ctype_digit($duration) || $duration < 0)
                            {
                                $context->local()->message(\Framework\Local::ERROR, "Time spent on project must be a number >= 0");
                            }
                            // check note text is not empty
                            elseif (empty($text))
                            {
                                $context->local()->message(\Framework\Local::ERROR, "Please ensure note text is not empty");
                            }
                            else 
                            {
                                $noteModel->text = $text;
                                $noteModel->duration = $duration;
                                $noteModel->user = $user; 
                                $noteModel->startDate = $context->utcnow();
                                $project->ownNoteList[] = $noteModel;
                                R::store( $project);
                                R::store($noteModel);
                                $context->local()->message(\Framework\Local::MESSAGE, "A new note has been created ");  
                            }     
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
                    // We are handling /project/id
                    return '@content/project.twig';   
                }       
            }
        }
 /**
 * Convert duration from seconds into days, hours, minutes array
 * 
 * Formats data to be passed into Twig
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