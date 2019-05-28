<?php
/**
 * @package    quickaddtomenu
 *
 * @author     artem <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

abstract class plgSystemQuickaddtomenuHelper{

    public static abstract function getItems(array $params);
    public static abstract function getLink(array $params);

    public static function getComponentId($component){

        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);

            $query->select($db->quoteName('extension_id'))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('name') .'='. $db->quote(trim($component)));
            $db->setQuery($query);
            $componentId = $db->loadResult();

            return $componentId;
        } catch (Exception $e) {
            throw new Exception ($e->getMessage(), $e->getCode());
        }
    }
}
