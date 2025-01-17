<?php
/**
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link	 	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class PopulateModelCategories extends JModel
{
	public function getData()
	{
	   $database = JFactory::getDBO();

		$database->setQuery("SELECT *, id as value, parent_id AS parent, name as text FROM #__categories WHERE section='com_handout'"  );
		$rows = (array) $database->loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}
		return $rows;
	}
}