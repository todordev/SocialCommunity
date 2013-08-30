<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 * @since       1.6
 */
class JFormFieldGender extends JFormFieldList {
    
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'Gender';
    
    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions(){
        
        // Initialize variables.
        $options = array();
        
        $options[] = JHTML::_('select.option', '',       JText::_("COM_SOCIALCOMMUNITY_SELECT_GENDER"), 'value', 'text');
		$options[] = JHTML::_('select.option', 'male',   JText::_("COM_SOCIALCOMMUNITY_GENDER_MALE"), 'value', 'text');
		$options[] = JHTML::_('select.option', 'female', JText::_("COM_SOCIALCOMMUNITY_GENDER_FEMALE"), 'value', 'text');
		
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        
        return $options;
    }
}
