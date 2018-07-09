<?php
/**
 * A model class for the RedBean object FWConfig
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2018 Newcastle University
 *
 */
    namespace Model;
/**
 * A class implementing a RedBean model for Page beans
 */
/**
 * @var string   The type of the bean that stores roles for this page
 */
    class FWConfig extends \RedBeanPHP\SimpleModel
    {
/**
 * Add a new FWConfig bean
 *
 * @param object    $context    The context object
 *
 * @return void
 */
        public static function add($context)
        {
            $fdt = $context->formdata();
            $name = $fdt->mustpost('name');
            $bn = \R::findOne('fwconfig', 'name=?', [$name]);
            if (is_object($bn))
            {
                $context->web()->bad();
            }
            $bn = \R::dispense('fwconfig');
            $bn->name = $name;
            $bn->value = $fdt->mustpost('value');
            $bn->local = $fdt->post('local', 0);
            $bn->fixed = 0;
            echo \R::store($bn);
        }
    }
?>