<?php
/**
 * @package      SocialCommunity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Component Route Helper that help to find a menu item.
 * IMPORTANT: It help us to find right MENU ITEM.
 *
 * Use router ...BuildRoute to build a link
 *
 * @static
 * @package        SocialCommunity
 * @subpackage     Components
 * @since          1.5
 */
abstract class SocialCommunityHelperRoute
{

    protected static $component = "com_socialcommunity";
    protected static $lookup;

    /**
     * This method route profile
     *
     * @param    int    User Id
     */
    public static function getProfileRoute($id)
    {

        $needles = array(
            'profile' => array(0)
        );

        // Create the link
        $link = 'index.php?option=com_socialcommunity&view=profile&id=' . $id;

        // Looking for menu item (Itemid)
        if ($item = self::_findItem($needles)) {
            $link .= '&Itemid=' . $item;
        } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
            $link .= '&Itemid=' . $item;
        }

        return $link;
    }

    /**
     * @param    string $layout
     *
     * @return URL to form
     */
    public static function getFormRoute($layout = null)
    {

        $needles = array(
            'form' => array(0)
        );

        //Create the link
        $link = 'index.php?option=com_socialcommunity&view=form';

        if (!empty($layout)) {
            $link .= "&layout=" . $layout;
        }

        // Looking for menu item (Itemid)
        if ($item = self::_findItem($needles)) {
            $link .= '&Itemid=' . $item;
        } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
            $link .= '&Itemid=' . $item;
        }

        return $link;
    }

    protected static function _findItem($needles = null)
    {

        $app   = JFactory::getApplication();
        $menus = $app->getMenu('site');

        // Prepare the reverse lookup array.
        // Collect all menu items and creat an array that contains
        // the ID from the query string of the menu item as a key,
        // and the menu item id (Itemid) as a value
        // Example:
        // array( "category" =>
        //     1(id) => 100 (Itemid),
        //     2(id) => 101 (Itemid)
        // );
        if (self::$lookup === null) {
            self::$lookup = array();

            $component = JComponentHelper::getComponent(self::$component);
            $items     = $menus->getItems('component_id', $component->id);

            if ($items) {
                foreach ($items as $item) {
                    if (isset($item->query) && isset($item->query['view'])) {
                        $view = $item->query['view'];

                        if (!isset(self::$lookup[$view])) {
                            self::$lookup[$view] = array();
                        }

                        if (isset($item->query['id'])) {
                            self::$lookup[$view][$item->query['id']] = $item->id;
                        } else { // If it is a root element that have no a request parameter ID ( categories, authors ), we set 0 for an key
                            self::$lookup[$view][0] = $item->id;
                        }
                    }
                }
            }
        }

        if ($needles) {

            foreach ($needles as $view => $ids) {
                if (isset(self::$lookup[$view])) {

                    foreach ($ids as $id) {
                        if (isset(self::$lookup[$view][(int)$id])) {
                            return self::$lookup[$view][(int)$id];
                        }
                    }

                }
            }

        } else {
            $active = $menus->getActive();
            if ($active) {
                return $active->id;
            }
        }

        return null;
    }

}
