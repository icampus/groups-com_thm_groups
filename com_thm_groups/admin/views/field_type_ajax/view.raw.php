<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Class loading persistent data into the view context
 * @link        www.thm.de
 */
class THM_GroupsViewField_Type_Ajax extends JViewLegacy
{
    /**
     * loads model data into view context
     *
     * @param   string $tpl the name of the template to be used
     *
     * @return void sets context parameters
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function display($tpl = null)
    {
        $model  = $this->getModel();
        $entity = JFactory::getApplication()->input->getCmd('task');

        switch ($entity) {
            case 'attribute':
                $fieldTypeName = $model->getNameByDynamicID();
                break;
            case 'dynType':
                $fieldTypeName = $model->getNameByID();
                break;
        }

        if (!empty($fieldTypeName)) {
            $functionName = 'get' . strtoupper($fieldTypeName) . 'Options';
            if (method_exists($this, $functionName)) {
                echo call_user_func([$this, $functionName]);
            }
        }
    }

    /**
     * Renders field set for the static type TEXT
     *
     * @return mixed
     */
    private function getTEXTOptions()
    {
        $form = $this->get('Form');

        return $form->renderFieldset('text');
    }

    /**
     * Renders field set for the static type TEXTFIELD
     *
     * @return mixed
     */
    private function getTEXTFIELDOptions()
    {
        $form = $this->get('Form');

        return $form->renderFieldset('textfield');
    }

    /**
     * Renders field set for the static type PICTURE
     *
     * @return mixed
     */
    private function getPICTUREOptions()
    {
        $form = $this->get('Form');

        return $form->renderFieldset('picture');
    }

    /**
     * Renders field set for the static type MULTISELECT
     *
     * @return mixed
     */
    private function getMULTISELECTOptions()
    {
        return '';
    }
}