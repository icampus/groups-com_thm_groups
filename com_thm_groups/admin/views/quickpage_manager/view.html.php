<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewQuickpage_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('thm_core.list.view');

/**
 * THM_GroupsViewQuickpage_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewQuickpage_Manager extends THM_CoreViewList
{

	public $items;

	public $pagination;

	public $state;

	/**
	 * Method to get display
	 *
	 * @param   Object $tpl template
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
		JToolBarHelper::title(
			JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_QUICKPAGE_MANAGER'), 'quickpage_manager'
		);

		$user         = JFactory::getUser();
		$rootCategory = THMLibThmQuickpages::getQuickpagesRootCategory();

		if ($user->authorise('core.manage', 'com_thm_groups') AND !empty($rootCategory))
		{
			JToolBarHelper::publishList('quickpage.qpPublish', 'COM_THM_GROUPS_PUBLISH');
			JToolBarHelper::unpublishList('quickpage.qpUnpublish', 'COM_THM_GROUPS_UNPUBLISH');

			JToolBarHelper::publishList('quickpage.qpFeature', 'COM_THM_GROUPS_FEATURE');
			JToolBarHelper::unpublishList('quickpage.qpUnfeature', 'COM_THM_GROUPS_UNFEATURE');
		}

		if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_thm_groups');
		}
	}

	/**
	 * Adds styles and scripts to the document
	 *
	 * @return  void  modifies the document
	 */
	protected function modifyDocument()
	{
		parent::modifyDocument();
		JHtml::_('bootstrap.framework');
	}
}