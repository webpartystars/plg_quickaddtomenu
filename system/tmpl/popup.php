<?php
/**
 * @package    quickaddtomenu
 *
 * @author     artem <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

?>
<div id="quickaddmenupopup" tabindex="-1" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close novalidate" data-dismiss="modal"
                aria-label="<?php echo Text::_('JTOOLBAR_CLOSE'); ?>">
            <span aria-hidden="true">×</span>
        </button>
        <h3><?php echo Text::_('PLG_SYSTEM_QUICKADDTOMENU_MODAL_TITLE'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="control-group span6">
                    <div class="controls">
                        <label id="quickaddmenu-language-lbl" for="quickaddmenu-language-id" class="control-label">
                            <?php echo Text::_('PLG_SYSTEM_QUICKADDTOMENU_LANGUAGE_LABEL'); ?>
                        </label>
                        <select name="quickaddmenu[language_id]" class="inputbox" id="quickaddmenu-language-id">
                            <option value=""><?php echo Text::_('PLG_SYSTEM_QUICKADDTOMENU_LANGUAGE_NOCHANGE'); ?></option>
                            <?php echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text'); ?>
                        </select>
                    </div>
                </div>
                <div class="control-group span6">
                    <div class="controls">
                        <label id="batch-access-lbl" for="batch-access" class="control-label">
                            <?php echo Text::_('PLG_SYSTEM_QUICKADDTOMENU_ACCESS_LABEL'); ?>
                        </label>
                        <?php echo HTMLHelper::_(
                            'access.assetgrouplist',
                            'quickaddmenu[assetgroup_id]', '',
                            'class="inputbox"',
                            array(
                                'title' => Text::_('PLG_SYSTEM_QUICKADDTOMENU_ACCESS_NOCHANGE'),
                                'id' => 'quickaddmenu-access'
                            )
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group span6">
                    <label id="quickaddmenu-choose-menu-lbl" class="control-label" for="quickaddmenu-menu-id">
                        <?php echo Text::_('PLG_SYSTEM_QUICKADDTOMENU_MENU_LABEL'); ?>
                    </label>
                    <div class="controls">
                        <select required name="quickaddmenu[menu_id]" id="quickaddmenu-menu-id">
                            <option class="disabled" disabled selected value=""><?php echo Text::_('PLG_SYSTEM_QUICKADDTOMENU_MENU_CHOSE'); ?></option>
                            <?php echo HTMLHelper::_('select.options', HTMLHelper::_('menu.menuitems')); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="quickaddmenu[option]" value="<?php echo $this->option; ?>"/>
        <input type="hidden" name="quickaddmenu[view]" value="<?php echo $this->view; ?>"/>
        <?php if($this->id): ?>
            <input type="hidden" name="quickaddmenu[id]" value="<?php echo $this->id; ?>"/>
        <?php endif; ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn"
                onclick="quickaddmenu.clear()"
                data-dismiss="modal">
            <?php echo Text::_('JCANCEL')?>
        </button>
        <button type="submit" class="btn btn-success" onclick="quickaddmenu.submit(event);">
            <?php echo Text::_('JGLOBAL_BATCH_PROCESS')?>
        </button>
    </div>
</div>
