<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

/**
 * THM_GroupsViewAttribute_Manager class for component com_thm_groups
 */
class THM_GroupsViewAttribute_Manager extends THM_GroupsViewList
{
    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER'), 'attribute_manager'
        );

        JToolBarHelper::addNew('attribute.add', 'COM_THM_GROUPS_NEW', false);
        JToolBarHelper::editList('attribute.edit', 'COM_THM_GROUPS_EDIT');
        JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM', 'attribute.delete',
            'JTOOLBAR_DELETE');

        $user = JFactory::getUser();
        if (THM_GroupsHelperComponent::isAdmin()) {
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
