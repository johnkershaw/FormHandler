<?php

/**
 * Copyright (C) 2015 FormHandler
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 *
 * @package FormHandler
 * @subpackage Field
 */

namespace FormHandler\Field;

/**
 * class Percentage
 *
 * Create a percentage field
 *
 * @author Marien den Besten
 * @package FormHandler
 * @subpackage Field
 */
class Percentage extends Number
{
    /**
     * Constructor
     *
     * @param FormHandler $form The form where this field is located on
     * @param string $name The name of the field
     * @return Percentage
     * @author Marien den Besten
     */
    public function __construct(FormHandler $form, $name)
    {
        return parent::__construct($form, $name)
            ->setMin(0)
            ->setExtra('class="percentage-field"')
            ->setValidator(_FH_FLOAT);
    }
    
    /**
     * Set value
     * 
     * @param integer $value
     * @return Percentage
     */
    public function setValue($value, $forced = false)
    {
        if(is_object($value) && method_exists($value, 'get'))
        {
            $value = $value->get();
        }
        return parent::setValue($value, $forced);
    }

    /**
     * getField()
     *
     * Return the HTML of the field
     *
     * @return string the html
     * @access public
     * @author Teye Heimans
     */
    public function getField()
    {
        $this->setExtraAfter('%');

        // view mode enabled ?
        if($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue() .'%';
        }

        //formhandler can not handle objects yet
        return parent::getField();
    }
}