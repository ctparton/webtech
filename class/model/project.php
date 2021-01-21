<?php
/**
 * A model class for the RedBean object Project
 *
 * This includes helper methods to return some special
 * Project attributes that cannot be accessed by Twig!
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright 2020 Callum Parton
 * @package Framework
 * @subpackage SystemModel
 */
    namespace Model;

    use \Support\Context;
    class Project extends \RedBeanPHP\SimpleModel
    {
/**
 * Returns the array of notes associated with this project
 *
 * @return array
 */
        public function ownNoteList() : array
        {
            return $this->bean->ownNoteList;
        }
/**
 * Return the array of users associated with this project
 *
 * @return array
 */
        public function sharedUserList() : array
        {
            return $this->bean->sharedUserList;
        }
/**
 * Return the owner of this uplaod
 *
 * @return ?object
 */
        public function owner() : ?object
        {
            return $this->bean->user;
        }

/**
 * Automatically called by RedBean when store is called
 * this handles basic error checking when creating/updating project
 *
 * @param Context $context
 *
 * @throws \Framework\Exception\BadValue
 * @return void
 */
        public function update() : void
        {
            if (empty($this->bean->name) || empty($this->bean->description))
            {
                throw new \Framework\Exception\BadValue("Project Name and Project Description must have a value");
            }
            // Alphanumeric with spaces is valid
            if (!preg_match('/^[\p{L}\p{N} ]+$/', $this->bean->name))
            {
                throw new \Framework\Exception\BadValue("Cannot update, name must be alphanumeric ".$this->bean->name);
            }
        }
/**
 * Automatically called by RedBean when a user tries to delete a project, this method adds extended permission checking for that
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