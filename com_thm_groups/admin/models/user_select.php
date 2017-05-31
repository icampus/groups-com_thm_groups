<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelUser_Select
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * Class compiling a list of users
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 * @link        www.thm.de
 */
class THM_GroupsModelUser_Select extends THM_GroupsModelList
{
    protected $defaultOrdering = 'name';

    protected $defaultDirection = 'ASC';

    /**
     * sets variables and configuration data
     *
     * @param   array $config the configuration parameters
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array('username', 'name');
        }
        parent::__construct($config);
    }

    /**
     * Generates the query to be used to fill the output list
     *
     * @return  object  the JDatabaseQuery object
     */
    protected function getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query->select("id, name, username");
        $query->from('#__users');

        $columns = array('name', 'username');
        $this->setSearchFilter($query, $columns);
        $this->setValueFilters($query, $columns);

        $ordering  = $this->_db->escape($this->state->get('list.ordering', $this->defaultOrdering));
        $direction = $this->_db->escape($this->state->get('list.direction', $this->defaultDirection));
        $query->order("$ordering $direction");

        return $query;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    public function getItems()
    {
        $items = parent::getItems();

        return $this->processItems($items);
    }

    /**
     * Processes the items transforming them from objects to array entries suitable for output in HTML
     *
     * @param   array $items the array of items
     *
     * @return  array  an array of HTML data
     */
    private function processItems($items)
    {
        $return = array();
        if (empty($items))
        {
            return $return;
        }
        $index = 0;
        foreach ($items as $item)
        {
            $return[$index]['id']       = JHtml::_('grid.id', $index, $item->id);
            $return[$index]['name']     = $item->name;
            $return[$index]['username'] = $item->username;
            $index++;
        }

        return $return;
    }

    /**
     * Generates the headers to be used by the output table
     *
     * @return  array  the table headers
     */
    public function getHeaders()
    {
        $ordering  = $this->state->get('list.ordering', 'name');
        $direction = $this->state->get('list.direction', 'ASC');

        $headers             = array();
        $headers['id']       = '';
        $headers['name']     = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_NAME', 'name', $direction, $ordering);
        $headers['username'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_USERNAME', 'username', $direction, $ordering);

        return $headers;
    }
}
