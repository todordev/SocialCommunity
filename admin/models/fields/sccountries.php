<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form field class that loads countries as options,
 * using code with 4 letters for ID.
 *
 * @package      SocialCommunity
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldScCountries extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'sccountries';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id AS value, a.name AS text')
            ->from($db->quoteName('#__itpsc_countries', 'a'))
            ->order("a.name ASC");

        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
