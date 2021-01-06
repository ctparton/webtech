<?php
/**
 * A class that handles the update AJAX operation for updating a project bean via a form
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 */
    namespace Ajax;
    use \Framework\Web\StatusCodes;
/**
 * Attach operation
 *
 * It expects a URL of the form /ajax/update/BEAN_TYPE/BEAN_ID and to have a POST
 * of some form data
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
                throw new \Framework\Exception\BadValue('Invalid bean');
            }
            
            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                throw new \Framework\Exception\InternalError('No user');
            }

            $formData = $context->formdata('put');
            // Handle project update
            if ($type === 'project')
            {
                if ($formData->exists('pname')) 
                {
                    try 
                    {
                        $project = $context->load($type, $rest[2]);
                        try 
                        {
                            $projectName = $formData->mustfetch('pname');
                            $projectDesc = $formData->mustfetch('pdesc');
                            $change = FALSE;
    
                            // Alphanumeric with spaces is valid
                            if (!preg_match('/^[\p{L}\p{N} ]+$/', $projectName))
                            {
                                $context->web()->sendJSON("Cannot update, name must be alphanumeric ".$projectName, StatusCodes::HTTP_BAD_REQUEST);
                            }
                            else
                            {
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
                            }
                            if ($change)
                            {
                                \R::store( $project);
                                $context->web()->sendJSON("The project (".$projectName.") has been updated  ");
                            }
                        }
                        catch (\Framework\Exception\BadValue $e) 
                        {
                            $context->web()->sendJSON("Please ensure project has name and a description", StatusCodes::HTTP_BAD_REQUEST);
                        }
                    }
                    catch (\Framework\Exception\MissingBean $e)
                    {
                        $context->web()->sendJSON("Could not load project", StatusCodes::HTTP_BAD_REQUEST);
                    } 
                }
            }
            else
            {
                if ($formData->exists('notetext')) 
                {
                    try 
                    {
                        $note = $context->load($type, $rest[2]); 
   
                        $text = $formData->mustfetch('notetext');
                        $change = FALSE;
                        if ($note->text !== $text && !empty($text))
                        {
                            $note->text = $text;
                            $change = TRUE;
                        }
                        else 
                        {
                            $context->web()->sendJSON("Updated note cannot be empty and must be different!", StatusCodes::HTTP_BAD_REQUEST);
                        }
                        if ($change)
                        {
                            \R::store( $note);
                            $context->web()->sendJSON("Note updated with text ".$text);
                        }
                    }
                    catch (\Framework\Exception\MissingBean $e) 
                    {
                        $context->web()->sendJSON("Could not load note", StatusCodes::HTTP_BAD_REQUEST);
                    }
                } 
            } 
        }          
    }
?>