<?php
/**
 * A model class for the RedBean object Project
 *
 * This includes helper methods to return some special
 * Project attributes that cannot be accessed by Twig!
 *
 * @author Callum Parton <c.parton@ncl.ac.uk>
 * @copyright Newcastle University
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
    }
?>