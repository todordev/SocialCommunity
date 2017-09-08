<?php
/**
 * @package      Socialcommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use \Socialcommunity\Country\Data\Gateway\Joomla\Countries as CountriesGateway;
use \Socialcommunity\Country\Data\Countries;

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form field class that loads countries as options,
 * using code with 4 letters for ID.
 *
 * @package      Socialcommunity
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
        $key     = isset($this->element['key']) ? (string)$this->element['key'] : 'id';

        // Prepare conditions.
        $fieldCode = new \Prism\Database\Request\Field(['column' => 'code']);
        $fieldName = new \Prism\Database\Request\Field(['column' => 'name']);

        $fields    = new \Prism\Database\Request\Fields;
        $fields
            ->addField($fieldCode)
            ->addField($fieldName);

        $databaseRequest = new \Prism\Database\Request\Request;
        $databaseRequest->setFields($fields);

        $countries  = new Countries(new CountriesGateway(JFactory::getDbo()));
        $countries->load($databaseRequest);

        $options = $countries->toOptions($key, 'name');

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
