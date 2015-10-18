<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewDynamic_Type_Manager
 * @description THM_GroupsViewDynamic_Type_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('thm_core.list.view');

/**
 * THM_GroupsViewDynamic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewDynamic_Type_Manager extends THM_CoreViewList
{

    public $items;

    public $pagination;

    public $state;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
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
        $user = JFactory::getUser();

        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER'), 'dynamic_type_manager'
        );

        if ($user->authorise('core.create', 'com_thm_groups'))
        {
            JToolBarHelper::addNew('dynamic_type.add', 'COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER_ADD', false);
        }

        if ($user->authorise('core.edit', 'com_thm_groups'))
        {
            JToolBarHelper::editList('dynamic_type.edit', 'COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER_EDIT');
        }

        if ($user->authorise('core.delete', 'com_thm_groups'))
        {
            JToolBarHelper::deleteList('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER_REALLY_DELETE', 'dynamic_type.delete', 'JTOOLBAR_DELETE');
        }

        if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
