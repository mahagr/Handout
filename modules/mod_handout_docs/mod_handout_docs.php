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

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

// Attach the Handout stylesheet to the document head
JHTML::stylesheet('handout.css', 'components/com_handout/media/css/');

require_once JPATH_ROOT . '/administrator/components/com_handout/handout.class.php';

$handout = &HandoutFactory::getHandout();

require_once $handout->getPath('classes', 'model');

$params = $params;

// Get the parameters
$show_icon 		 = abs($params->def( 'show_icon', 1 ));
$show_counter	 = abs($params->def( 'show_counter', 1 ));
$show_category 	 = abs($params->def( 'show_category', 1 ));
$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
$text_pfx	 	 = $params->def( 'text_pfx', '' );
$text_sfx	 	 = $params->def( 'text_sfx', '' );

$class_prefix 	= "hmodule-prefix" . $moduleclass_sfx;
$class_suffix 	= "hmodule-suffix" . $moduleclass_sfx;

$is_mtree_listing = $params->get( 'is_mtree_listing', 0);

$can_display = true;
if ($is_mtree_listing) {
	//check to make sure this is a mtree listing page
	$link_id = JRequest::getVar('link_id', '');
	if ((JRequest::getVar('option') == 'com_mtree') && (JRequest::getVar('task') == 'viewlink') && ($link_id)) {
		$can_display = true;
	}
	else {
		$can_display = false;
	}
}

$menuid = $handout->getMenuId();

if ($can_display) {
	$rows = modHandoutdocsHelper::getDocs($params);
	require(JModuleHelper::getLayoutPath('mod_handout_docs'));
}
