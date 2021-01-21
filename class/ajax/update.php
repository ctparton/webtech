<?php
/**
 * A class that handles the update AJAX operation for updating a project or note bean via a form
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 */
    namespace Ajax;
    use \Framework\Web\StatusCodes;
/**
 * Update operation
 *
 * It expects a URL of the form /ajax/update/BEAN_TYPE/BEAN_ID and to have a PATCH
 * of some form data sent
 */
    final class Update extends \Framework\Ajax\Ajax
    {
/**
 * @var array If you want to use the permission checking functions. If you just want to control access
 *            then just put the list of contextname/rolename pairs in the result of requires.
 */
         private static $permissions = [];
/**
 * Return permission requirements. The version in the base class requires logins and adds nothing else.
 * If that is what you need then you can remove this method. This function is called from the base
 * class constructor when it does some permission checking.
 *
 * @return array
 */
        public function requires()
        {
            return [TRUE, []];
        }
/**
 * Submit a form with fields to edit
 *
 * @return void
 */
        public function handle() : void
        {
            $context = $this->context;
            $rest = $context->rest();
            $type = strtolower($rest[1]);
            if (!in_array($type, ['project', 'note']))
            {
                $context->web()->bad("Expecting BEAN_TYPE to be 'project' or 'note'");
                throw new \Framework\Exception\BadValue('Invalid bean');
            }
            
            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                $context->web()->sendJSON("You must be logged in to perform this action", StatusCodes::HTTP_UNAUTHORIZED);
                throw new \Framework\Exception\InternalError('No user');
            }

            $formData = $context->formdata('put');
            // Handle project update
            if ($type === 'project')
            {
                try 
                {
                    $project = $context->load($type, $rest[2]);
                    if ($context->user()->login !== $project->owner()->login)
                    {
                        $context->web()->noAccess("Only the owner of the project can update");
                    }
                    else 
                    {
                        if ($formData->exists('pname')) 
                        {      
                            try 
                            {
                                $projectName = $formData->mustfetch('pname');
                                $projectDesc = $formData->mustfetch('pdesc');
                                $change = FALSE;
            
                                if ($project->name !== $projectName && !empty($projectName))
                                {
                                    $project->name = $projectName;
                                    $change = TRUE;
                                }
                                if ($project->description !== $projectDesc && !empty($projectDesc))
                                {
                                    $project->description = $projectDesc;
                                    $change = TRUE;
                                }
                                
                                if ($change)
                                {
                                    \R::store( $project);
                                    $context->web()->sendJSON("The project (".$projectName.") has been updated");
                                }
                            }
                            catch (\Framework\Exception\BadValue $e) 
                            {
                                $context->web()->bad($e->getMessage());
                            }    
                        }
                        else if ($formData->exists('contributor'))
                        {                     
                            try 
                            {
                                $contributor = $formData->mustfetch('contributor');
                                $siteinfo = \Support\SiteInfo::getinstance();
        
                                // check if user is not current user or already on project
                                if ($contributor !== $context->user()->login && !in_array($contributor, array_map(function($e) { return $e->login; }, $project->sharedUserList)))
                                {
                                    // if user is a registered user
                                    if(in_array($contributor, array_map(function($e){ return $e->login; }, $siteinfo->users()))) 
                                    {
                                        // filter users to get the added user
                                        $addedUser = array_filter($siteinfo->users(), function($e) use (&$contributor) {return $e->login === $contributor;});
                                        if (count($addedUser) == 1)
                                        {
                                            $user = $context->load('user', (int) $addedUser[array_key_first($addedUser)]->getID(), TRUE);
                                            $project->sharedUserList[] = $user;
                                            \R::store($project);
                                            $context->web()->sendJSON("Added new contributor to project");
                                        } 
                                        else 
                                        {
                                            $context->web()->bad('username '.$contributor.' does not exist or is not unique');
                                        }                           
                                    }
                                    else 
                                    {
                                        $context->web()->bad('Can not find user with username '.$contributor);
                                    }
                                }
                                else 
                                {
                                    $context->web()->bad($contributor.' is already on the project');
                                }
                                
                            }
                            catch (\Framework\Exception\BadValue $e) 
                            {
                                $context->web()->bad("Must complete contributors field");
                            }
                        }
                        else
                        {
                            $context->web()->bad("Form type not expected");
                        }
                    }
                }
                catch (\Framework\Exception\MissingBean $e)
                {
                    $context->web()->bad("Could not load project with this ID");
                }        
            }
            else
            {
                if ($formData->exists('notetext')) 
                {
                    try 
                    {
                        $note = $context->load($type, $rest[2]); 
                        if ($context->user()->login !== $note->owner()->login)
                        {
                            $context->web()->noAccess("Only the owner of the note can update");
                        }
                        else 
                        {
                            try 
                            {
                                $text = $formData->mustfetch('notetext');          
                                $change = FALSE;
                                if ($note->text !== $text && !empty($text))
                                {
                                    $note->text = $text;
                                    $change = TRUE;
                                }
                                else 
                                {
                                    $context->web()->bad("Updated note cannot be empty and must be different!");
                                }
                                if ($change)
                                {
                                    \R::store( $note);
                                    $context->web()->sendJSON("Note updated with text ".$text);
                                }
                            }
                            catch (\Framework\Exception\BadValue $e) 
                            {
                                $context->web()->bad($e->getMessage());
                            }
                        }
                       
                    }
                    catch (\Framework\Exception\MissingBean $e) 
                    {
                        $context->web()->bad("Could not load note");
                    }
                }
                else 
                {
                    $context->web()->bad("Form type not expected");
                } 
            } 
        }          
    }
?>