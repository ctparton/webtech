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
/**
 * Upload table stores info about files that have been uploaded...
 * @psalm-suppress UnusedClass
 */
    class Project extends \RedBeanPHP\SimpleModel
    {
        use \ModelExtend\Upload;
/**
 * Return the owner of this uplaod
 *
 * @return ?object
 */
        public function sharedUserList() : array
        {
            return $this->bean->sharedUserList;
        }

        public function owner() : ?object
        {
            return $this->bean->user;
        }
    }
?>