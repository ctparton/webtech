<?php
/**
 * A class that contains code to handle any requests for /create/
 * It will allow a signed in user to create new projects
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \R;
/**
 * Support /create/
 */
    class Create extends \Framework\Siteaction
    {
/**
 * Handle create operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $formData = $context->formdata('post');
            if (!$context->hasuser())
            { 
                // If no user logged in, throw error. Should not happen given this page has a login requirement
                throw new \Framework\Exception\InternalError('No user');
            }
            if ($formData->exists('pname')) 
            {
     
                try 
                {
                    $user = $context->load('user', (int) $context->user()->getID(), TRUE);
                    try 
                    {
                        $projectName = $formData->mustfetch('pname');
                        $projectDesc = $formData->mustfetch('pdesc');
                        if (!ctype_alnum($projectName))
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
    }
?>