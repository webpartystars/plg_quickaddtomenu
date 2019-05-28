<?php
/**
 * @package    quickaddtomenu
 *
 * @author     artem <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Filesystem\Folder;

defined('_JEXEC') or die;

/**
 * Quickaddtomenu plugin.
 *
 * @package  quickaddtomenu
 * @since    1.0
 */
class plgSystemQuickaddtomenu extends CMSPlugin
{
    /**
     * Application object
     *
     * @var    CMSApplication
     * @since  1.0
     */
    protected $app;

    protected $user;

    protected $allowedComponents;

    protected $componentsDir;

    protected $option;
    protected $layout;
    protected $id;
    protected $view;

    /**
     * Load plugin language file automatically so that it can be used inside component
     *
     * @var    boolean
     * @since  3.9.0
     */
    protected $autoloadLanguage = true;

    public function __construct($subject, array $config = array())
    {
        try {
            $this->app = Factory::getApplication();
        } catch (Exception $e) {
            throw new Exception ($e->getMessage(), $e->getCode());
        }

        $this->user = Factory::getUser();
        $this->componentsDir = JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'quickaddtomenu' . DIRECTORY_SEPARATOR . 'components';
        $this->allowedComponents = Folder::folders($this->componentsDir);
        $this->option = $this->app->input->get('option');
        $this->layout = $this->app->input->get( 'layout' );
        $this->id = $this->app->input->get( 'id' );
        $this->view = $this->app->input->get( 'view' );

        switch ($this->option){
            case 'com_content':
                $this->view = 'article';
                break;
        }

        parent::__construct($subject, $config);
    }

    /**
     * onBeforeRender.
     *
     * @since   1.0
     */
    public function onBeforeRender()
    {
        if(!$this->checkAccess(true)){
            return false;
        }

        $view = $this->app->input->get('view');

        $html = [];
        $html[] = '<button';
        $html[] = 'type="button"';
        $html[] = 'data-toggle="modal"';

        if (in_array($view, [null, 'articles'])) {
            $html[] = 'onclick="if (document.adminForm.boxchecked.value==0){alert(Joomla.JText._(\'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST\'));}else{jQuery( \'#quickaddmenupopup\' ).modal(\'show\'); return true;}"';
        } else {
            $html[] = 'onclick="jQuery( \'#quickaddmenupopup\' ).modal(\'show\'); return true;"';
        }

        $html[] = 'class="btn btn-small"';
        $html[] = '>';
        $html[] = '<span class="icon-plus" aria-hidden="true"></span>';
        $html[] = Text::_('PLG_SYSTEM_QUICKADDTOMENU_TOOLBAR_ADD');
        $html[] = '</button>';

        $toolbar = ToolBar::getInstance('toolbar');
        $toolbar->appendButton('Custom', implode(' ', $html));

        HTMLHelper::_('script', 'plg_quickaddtomenu/script.js', array('version' => 'auto', 'relative' => true));
    }

    public function onAfterRender()
    {
        if(!$this->checkAccess(true)){
            return false;
        }

        $body = $this->app->getBody();
        $lnEnd = Factory::getDocument()->_getLineEnd();

        ob_start();
        require_once PluginHelper::getLayoutPath('system', 'quickaddtomenu', 'popup');
        $popup = ob_get_clean();
        $body = preg_replace('#\<(((?!\>).)*)(id\s?=\s?\"adminForm\")(((?!\>).)*)\>#', '$0' . $lnEnd . $popup, $body);
        $this->app->setBody($body);
    }

    public function onAjaxQuickaddtomenu()
    {
        if(!$this->checkAccess(false)){
            return false;
        }

        $input = $this->app->input;
        $data = $input->post->getArray();

        ob_end_clean();
        if (!isset($data['quickaddmenu']['menu_id']) || empty($data['quickaddmenu']['menu_id'])) {
            echo new JsonResponse(null, Text::_('НЕТ_МЕНЮ'), true);
            die();
        }

        $option = $data['quickaddmenu']['option'];
        if (empty($option) || !in_array($option, $this->allowedComponents)) {
            echo new JsonResponse(null, Text::_('НЕТ_option'), true);
            die();
        }

        $view = $data['quickaddmenu']['view'];
        if (empty($view)) {
            echo new JsonResponse(null, Text::_('НЕТ_view'), true);
            die();
        }

        try {
            $menutype = explode('.', $data['quickaddmenu']['menu_id']);

            JLoader::register('quickaddtomenuHelper', __DIR__ . '/components/'.$option.'/'.$view.'.php');

            $componentId = (int) quickaddtomenuHelper::getComponentId($option);
            if(!$componentId){
                throw new Exception('Компонент не найден');
            }

            $menuData = array(
                'menutype' => $menutype[0],
                'parent_id' => $menutype[1],
                'type' => 'component',
                'component_id' => $componentId,
                'published' => 1,
                'params' => []
            );


            if(isset($data['quickaddmenu']['id']) && !empty($data['quickaddmenu']['id'])){
                $iParams['ids'][] = $data['quickaddmenu']['id'];
            } else if(isset($data['cid']) && !empty($data['cid'])){
                $iParams['ids'] = $data['cid'];
            } else {
                echo new JsonResponse(null, Text::_('НЕТ_МАТЕРИАЛОВ'), true);
                die();
            }

            $items = quickaddtomenuHelper::getItems($iParams);

            if(!count($items)){
                echo new JsonResponse(null, Text::_('НЕТ_МАТЕРИАЛОВ'), true);
                die();
            }

            foreach ($items as &$item){
                $menuData['language'] = !empty($data['quickaddmenu']['language_id']) ? $data['quickaddmenu']['language_id'] : $item['language'];
                $menuData['access'] = !empty($data['quickaddmenu']['assetgroup_id']) ? $data['quickaddmenu']['assetgroup_id'] : $item['access'];
                $menuData['link'] = quickaddtomenuHelper::getLink(['id'=>$item['id']]);
                $menuData['title'] = $item['title'];
                $menuData['alias'] = $item['alias'];
                $menuData['id'] = 0;
                $menuData['request']['id'] = $item['id'];
                $this->addMenuItem($menuData);
            }

            echo new JResponseJson($data);
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }
        die();
    }

    protected function addMenuItem($data){
        BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/models');
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_menus/models/forms');
        Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
        /* @var MenusModelItem $menuItem */
        $menuItem = BaseDatabaseModel::getInstance('Item', 'MenusModel');
        // Set the menutype should we need it.
        if ($data['menutype'] !== '')
        {
            $this->app->input->set('menutype', $data['menutype']);
        }
        $form = $menuItem->getForm($data);
        $data = $menuItem->validate($form, $data);
        $menuItem->save($data);
    }

    protected function checkAccess($checkOption = false)
    {
        if (!$this->app->isClient('administrator')) {
            return false;
        }

        if (!$this->user->authorise('core.create', 'com_menus') || !$this->user->authorise('core.edit', 'com_menus')) {
            return false;
        }

        if ($checkOption && !in_array($this->option, $this->allowedComponents)) {
            return false;
        }

        return true;
    }
}
