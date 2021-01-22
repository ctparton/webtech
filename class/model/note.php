<?php
/**
 * A model class for the RedBean object Note
 *
 * This includes helper methods to return some special
 * Note attributes that cannot be accessed by Twig for some reason!
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 * @package Framework
 * @subpackage SystemModel
 */
    namespace Model;

    use \Support\Context;

    class Note extends \RedBeanPHP\SimpleModel
    {
        
/**
 * Return the uploads attatched to this note
 *
 * @return array
 */
        public function sharedUploadList() : array
        {
            return $this->bean->sharedUploadList;
        }
/**
 * Return the creator of the note
 *
 * @return array
 */
        public function owner() : ?object
        {
            return $this->bean->user;
        }

/**
 * Automatically called by RedBean when store is called
 * this handles basic error checking when creating/updating note
 *
 * @param Context $context
 *
 * @throws \Framework\Exception\BadValue
 * @return void
 */
        public function update() : void
        {
            if (empty($this->bean->text))
            {
                throw new \Framework\Exception\BadValue("Please ensure note text is not empty");
            }
            // Note must have valid duration
            if (!ctype_digit($this->bean->duration) || $this->bean->duration < 0)
            {
                throw new \Framework\Exception\BadValue("Time spent on project must be a number >= 0");
            }
        }
/**
 * Automatically called by RedBean when a user tries to delete a note, this method adds extended permission checking for that
 * in case a user bypasses the existing frontend provisions
 *
 * @param Context $context
 *
 * @throws \Framework\Exception\Forbidden
 * @return void
 */
        public function delete() : void
        {
            $context = Context::getinstance();
            if ($context->user()->login !== $this->bean->owner()->login)
            {
                throw new \Framework\Exception\Forbidden('Permission Denied');
            }
            
        }
    }
?>