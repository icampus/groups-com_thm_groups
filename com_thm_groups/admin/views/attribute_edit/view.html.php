<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/views/edit.php';

/**
 * THM_GroupsViewAttribute_Edit class for component com_thm_groups
 */
class THM_GroupsViewAttribute_Edit extends THM_GroupsViewEdit
{
    /**
     * Method to get display
     *
     * @param   Object $tpl template  (default: null)
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups')) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        $input = JFactory::getApplication()->input;
        $id    = $input->getInt('id', 0);

        // Disable editing of the selected dynamic type
        if ($id != 0) {
            $form = $this->get('Form');
            $form->setFieldAttribute('dynamic_typeID', 'readonly', 'true');
        }

        parent::display($tpl);
    }

    /**
     * Adds styles and scripts to the document
     *
     * @return  void  modifies the document
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();
        $document = JFactory::getDocument();
        $document->addScript(JUri::root() . "/media/com_thm_groups/js/attribute_edit.js");
    }

    /**
     * Adds the toolbar to the page
     *
     * @return void
     */
    public function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $title = $this->item->id == 0 ? JText::_('COM_THM_GROUPS_ATTRIBUTE_EDIT_NEW_TITLE') : JText::_('COM_THM_GROUPS_ATTRIBUTE_EDIT_EDIT_TITLE');

        JToolBarHelper::title($title, 'edit');

        // First argument is [controller.function] that will be executed
        JToolBarHelper::apply('attribute.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('attribute.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('attribute.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('attribute.cancel', 'JTOOLBAR_CLOSE');
    }
}
