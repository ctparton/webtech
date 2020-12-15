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
            if ($formData->exists('pname')) 
            {
                try 
                {
                    $user = $context->load('user', (int) $context->user()->id, TRUE);                 
                    $projectName = $formData->mustfetch('pname');
                    $projectDesc = $formData->fetch('pdesc');
                    $projectModel = R::dispense('project');
                    $projectModel->sharedUserList[] = $user;
                    $projectModel->user = $user; 
                    $projectModel->name = $projectName;
                    $projectModel->description = $projectDesc;
                    $projectModel->startDate = $context->utcnow();
                    $projectId = R::store( $projectModel);
                    $context->local()->message(\Framework\Local::MESSAGE, "A project (".$projectName.") has been created");
                }
                catch (\Framework\Exception\BadValue $e) 
                {
                    $context->local()->message(\Framework\Local::ERROR, "A new project must have a name");
                }
            } 
            return '@content/create.twig';
        }
    }
?>