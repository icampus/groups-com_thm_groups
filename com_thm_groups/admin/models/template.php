<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelTemplate
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/assets/helpers/database_compare_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelTemplate extends JModelLegacy
{
    /**
     * Method to perform batch operations on an item or a set of items.
     * TODO make generic function which handle all types of batch operations
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     */
    public function batch()
    {
        $input = JFactory::getApplication()->input;

        // Array with action command
        $action = $input->get('batch_action', array(), 'array');
        $groupIDs = $input->get('batch_id', array(), 'array');
        $rawProfileIDs  = $input->get('cid', array(), 'array');

        // Sanitize group ids.
        $profileIDs = array_unique($rawProfileIDs);
        Joomla\Utilities\ArrayHelper::toInteger($profileIDs);

        // Remove any values of zero.
        $zeroIndex = array_search(0, $profileIDs, true);
        if ($zeroIndex !== false)
        {
            unset($profileIDs[$zeroIndex]);
        }

        if (empty($profileIDs))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'warning');
            return false;
        }

        $done = false;
        if (!empty($groupIDs))
        {
            $cmd = $action[0];

            if (!$this->batchProfile($groupIDs, $profileIDs, $cmd))
            {
                return false;
            }

            $done = true;
        }

        if (!$done)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 'error');
            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Perform batch operations
     *
     * @param   array   $groupIDs    The role IDs which assignments are being edited
     * @param   array   $profileIDs  An array of group IDs on which to operate
     * @param   string  $action       The action to perform
     *
     * @return  boolean  True on success, false on failure
     *
     */
    public function batchProfile($groupIDs, $profileIDs, $action)
    {
        Joomla\Utilities\ArrayHelper::toInteger($groupIDs);
        Joomla\Utilities\ArrayHelper::toInteger($profileIDs);

        if ($action == 'del')
        {
            return $this->batchDelete($groupIDs, $profileIDs);
        }
        return $this->batchAssociation($groupIDs, $profileIDs);
    }

    /**
     * Associates groups with the selected profile templates
     *
     * @param   array  $groupIDs    the ids of the groups to be associated
     * @param   array  $templateIDs  the ids of the profiles to which the groups are to be associated
     *
     * @return  bool  true on success, otherwise false
     */
    private function batchAssociation($groupIDs, $templateIDs)
    {
        $query = $this->_db->getQuery(true);

        // First, we need to check if the role is already assigned to a group
        $query->select('profileID, usergroupsID');
        $query->from('#__thm_groups_profile_usergroups');
        $query->where('profileID IN (' . implode(',', $templateIDs) . ')');
        $query->order('profileID');

        $this->_db->setQuery((string) $query);

        try
        {
            $templateGroups = $this->_db->loadObjectList();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }

        // Build an array with unique templates and their associated groups
        $templates = array();
        foreach ($templateGroups as $templateGroup)
        {
            $templates[$templateGroup->profileID][] = (int) $templateGroup->usergroupsID;
        }

        // Contains groups and roles to insert in DB
        $insertValues = array();
        foreach ($templateIDs as $templateID)
        {
            foreach ($groupIDs as $groupID)
            {
                $insertValues[$templateID][] = $groupID;
            }
        }

        // Removes groups already associated in the manner requested
        THM_GroupsHelperDatabase_Compare::filterInsertValues($insertValues, $templates);

        // All associations to be created already exist
        if (empty($insertValues))
        {
            return true;
        }

        $query = $this->_db->getQuery(true);
        $query->insert('#__thm_groups_profile_usergroups');
        $query->columns(array($this->_db->quoteName('profileID'), $this->_db->quoteName('usergroupsID')));

        $processingNeeded = false;
        foreach ($insertValues as $templateID => $groups)
        {
            if (empty($groups))
            {
                continue;
            }
            foreach ($groups as $groupID)
            {
                $query->values($templateID . ',' . $groupID);
            }
            $processingNeeded = true;
        }

        // If there are no roles to process, throw an error to notify the user
        if (!$processingNeeded)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ASSOCIATIONS_ALREADY_EXIST'), 'message');
            return true;
        }

        $this->_db->setQuery((string) $query);

        try
        {
            $this->_db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }
        return true;
    }

    /**
     * Removes the association of groups with the selected profile templates
     *
     * @param   array  $groupIDs    the ids of the groups whose associations are to be removed
     * @param   array  $profileIDs  the ids of the profiles from which the associations are to be removed
     *
     * @return  bool  true on success, otherwise false
     */
    private function batchDelete($groupIDs, $profileIDs)
    {
        $query = $this->_db->getQuery(true);

        // Remove groups from the profile
        $query->delete('#__thm_groups_profile_usergroups');
        $query->where('profileID' . ' IN (' . implode(',', $profileIDs) . ')');
        $query->where('usergroupsID' . ' IN (' . implode(',', $groupIDs) . ')');

        $this->_db->setQuery((string) $query);

        try
        {
            $this->_db->execute();
        }
        catch (RuntimeException $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
        return true;
    }

    /**
     * Delete item
     *
     * @return mixed
     */
    public function delete()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');

        $query = $this->_db->getQuery(true);

        $conditions = array(
            $this->_db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($this->_db->quoteName('#__thm_groups_profile'));
        $query->where($conditions);

        $this->_db->setQuery($query);

        return $result = $this->_db->execute();
    }

    /**
     * Deletes a group from a profile by clicking on
     * delete icon near profile name
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteGroup()
    {
        $input = JFactory::getApplication()->input;

        $profileID = $input->getInt('p_id');
        $groupID = $input->getInt('g_id');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_profile_usergroups')
            ->where("profileID = '$profileID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery((string) $query);

        try
        {
            $this->_db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }

        return true;
    }

    /**
     * Get the last order position of a profile
     *
     * @return Integer
     */
    public function getLastPosition()
    {
        $query = $this->_db->getQuery(true);

        $query
            ->select(" MAX(`order`)  as 'order'")
            ->from('#__thm_groups_profile');

        $this->_db->setQuery($query);
        $lastPosition = $this->_db->loadObject();

        return $lastPosition->order;
    }

    /**
     * Saves the profile templates
     *
     * @param   bool  $new  whether or not the template should explicitly be saved as a new entry
     *
     * @return  bool true on success, otherwise false
     */
    public function save($new = false)
    {
        $this->_db->transactionStart();
        $input = JFactory::getApplication()->input;
        $data = $input->get('jform', array(), 'array');

        if ($new)
        {
            $data['id'] = '';
        }

        $templateID = intval($data['id']);
        $attributeList = $input->get('attributeList', '', 'string');
        $attributeJSON = (array) json_decode($attributeList);

        if ($templateID == 0)
        {
            $data['order'] = $this->getLastPosition() + 1;
        }
        $template = JTable::getInstance('Template', 'Table');

        $success = $template->save($data);

        if (!$success)
        {
            $this->_db->transactionRollback();
            return false;
        }

        $this->_db->transactionCommit();

        if (!empty($attributeJSON))
        {
            $deleteQuery = $this->_db->getQuery(true);
            $deleteQuery->delete('#__thm_groups_profile_attribute');
            $deleteQuery->where('profileID =' . $template->id);
            $this->_db->setQuery($deleteQuery);

            try
            {
                $this->_db->execute();
            }
            catch (Exception $exc)
            {
                JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
                return $template->id;
            }

            $putQuery = $this->_db->getQuery(true);
            $putQuery->insert('#__thm_groups_profile_attribute');
            $columns = array('profileID', 'attributeID', 'order', 'params');

            // TODO This quoteName has to be here because 'order' is an sql keyword. => Alter column name!
            $putQuery->columns($this->_db->quoteName($columns));
            foreach ($attributeJSON as $index => $value )
            {
                $attributeID = intval($index);
                $order = intval($value->order);
                $params = json_encode($value->params);
                $putQuery->values("'$template->id', '$attributeID', '$order', '$params'");
            }

            $this->_db->setQuery($putQuery);
            $success = $this->_db->execute();
            if (!$success)
            {
                return false;
            }
        }
        return $template->id;
    }
}