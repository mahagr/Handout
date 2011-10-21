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

class PopulateDocman
{
	public static function isInstalled()
	{
		return file_exists(JPATH_ADMINISTRATOR.'/components/com_handout/handout.class.php');
	}

	public static function get()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_handout/handout.class.php';
		global $_HANDOUT, $_DMUSER;
		if(!is_object($_HANDOUT)) {
			$_HANDOUT = HandoutFactory::getHandout();
			$_DMUSER = $_HANDOUT->getUser();
		}

		return $_HANDOUT;
	}

	public static function checkVersion()
	{
		self::get();
		return (version_compare(_DM_VERSION, '1.5', '>=') && version_compare(_DM_VERSION, '1.6', '<'));
	}

	public static function getVersion()
	{
		if(self::isInstalled())
		{
			self::get();
			return _DM_VERSION;
		}
		return 0;
	}
}