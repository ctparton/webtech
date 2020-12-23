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
/**
 * Upload table stores info about files that have been uploaded...
 * @psalm-suppress UnusedClass
 */
    class Note extends \RedBeanPHP\SimpleModel
    {
        use \ModelExtend\Upload;
/**
 * Return the uploads attatched to this note
 *
 * @return array
 */
        public function sharedUploadList() : array
        {
            return $this->bean->sharedUploadList;
        }

        public function owner() : ?object
        {
            return $this->bean->user;
        }
    }
?>