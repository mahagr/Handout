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

$pw = JRequest::getCmd('pw', '', 'get');
if($pw == '') {
	echo 'Handout Populate is not a frontend component. See the configuration panel for cron commands';
	return;
}
if($pw !== md5(JFactory::getApplication()->getCfg('secret')))
{
	die('Bad password');
}

require_once JPATH_ADMINISTRATOR.'/components/com_populate/helpers/handout.php' ;

require_once JPATH_COMPONENT.'/controllers/documents.php';
$controller	= new PopulateControllerDocuments;
$controller->execute('assign');
die;