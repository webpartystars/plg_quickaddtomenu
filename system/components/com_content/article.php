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

JLoader::register('plgSystemQuickaddtomenuHelper', __DIR__ . '/../../helper.php');
class quickaddtomenuHelper extends plgSystemQuickaddtomenuHelper{
    public static function getItems(array $params){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id', 'title', 'alias', 'language', 'access')))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id'). ' IN ('. implode(',', $params['ids']). ')');

        $db->setQuery($query);

        return $db->loadAssocList();
    }

    public static function getLink(array $params)
    {
        return 'index.php?option=com_content&view=article&id='.$params['id'];
    }
}
