<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	Improved by JoomDOC by Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/handout.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/handout.php';

$task = JRequest::getCmd('task');

$handout = HandoutFactory::getHandout();
$lang = &JFactory::getLanguage();
$lang->load('com_handout', JPATH_ADMINISTRATOR);

if ($task == 'migration') {
	$src = JRequest::getCmd('migratefrom', '');
	require_once JPATH_COMPONENT_ADMINISTRATOR . '/handout.migration.php';
	$migrator = &HandoutMigration::getInstance($src);
	$migrator->migrate();
}

$database = &JFactory::getDBO();
$user = &JFactory::getUser();

require_once $handout->getPath('classes', 'html');
require_once $handout->getPath('classes', 'utils');
require_once $handout->getPath('classes', 'token');

$cid = JRequest::getVar('cid', array());
if (!is_array($cid)) {
	$cid = array(0);
}
$gid = (int) JRequest::getVar('gid', '0');

// retrieve some expected url (or form) arguments

$pend = JRequest::getVar('pend', 'no');
$updatedoc = JRequest::getVar('updatedoc', '0');
$sort = JRequest::getVar('sort', '0');
$view_type = JRequest::getVar('view', 1);

$section = JRequest::getCmd('section');

// add stylesheet

$css = JUri::base(true) . '/components/com_handout/includes/handout.css';
$doc = JFactory::getDocument();
$doc->addCustomTag('<link rel="stylesheet" type="text/css" media="all" href="' . $css . '" />');

// execute task

if (($task == 'cpanel') || ($section == null)) {
	if (J16PLUS) {
		HandoutHelper::addSubmenu('handout');
	}
	include_once $handout->getPath('includes', 'handout');
}
else {
	if (J16PLUS) {
		HandoutHelper::addSubmenu($section);
	}
	include_once $handout->getPath('includes', $section);
}
