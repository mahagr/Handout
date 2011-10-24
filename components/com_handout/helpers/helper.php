<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

class HandoutHelper {

	function getMenuParams() {
		jimport ( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$Itemid = JRequest::getInt ( 'Itemid' );
		return $menu->getParams ( $Itemid );
	}

	function fetchMenu($gid = 0) {
		$handout = &HandoutFactory::getHandout ();
		$handoutUser = &HandoutFactory::getHandoutUser ();
		// create links
		$links = new StdClass ( );
		$links->home = HandoutHelper::_taskLink ( null );
		$links->search = HandoutHelper::_taskLink ( 'search_form' );
		$links->upload = HandoutHelper::_taskLink ( 'upload', $gid );

		// create perms
		$perms = new StdClass ( );
		$perms->view = true;
		$perms->search = true;
		$perms->upload = $handoutUser->canUpload ();

		$returnArray = array($links, $perms);
		return $returnArray;
	}

	function fetchPathWay($id) {
		if (! $id > 0) {
			return;
		}

		// get the category ancestors
		$ancestors = & HANDOUT_Cats::getAncestors ( $id );

		// add home link
		$home = new StdClass ( );
		$home->name = COM_HANDOUT_DOWNLOADS_HOME;
		$home->title = COM_HANDOUT_DOWNLOADS_HOME;
		$home->link = HANDOUT_Utils::taskLink ( '' );

		$ancestors [] = &$home;
		// reverse the array
		$ancestors = array_reverse ( $ancestors );

		$returnArray = array($ancestors);
		return $returnArray;
	}

	function _taskLink($task, $gid = '', $params = null, $sef = true) {
		return HANDOUT_Utils::taskLink ( $task, $gid, $params, $sef );
	}

	function fetchPageNav($gid) {
		$handoutUser = &HandoutFactory::getHandoutUser ();
		$handout = &HandoutFactory::getHandout ();

		$limit = JRequest::getInt ( 'limit', $handout->getCfg ( 'perpage' ) );
		$total = HANDOUT_Cats::countDocsInCatByUser ( $gid, $handoutUser );

		if ($total <= $limit) {
			return;
		}

		$Itemid = JRequest::getInt ( 'Itemid' );
		$ordering = JRequest::getVar ( 'ordering', $handout->getCfg ( 'default_order' ) );
		$direction = strtoupper ( JRequest::getVar ( 'dir', $handout->getCfg ( 'default_order2' ) ) );
		$limitstart = JRequest::getInt ( 'limitstart' );

		jimport ( 'joomla.html.pagination' );
		$pageNav = new JPagination ( $total, $limitstart, $limit );

		 // where is $link used?
		//$link = 'index.php?option=com_handout&amp;task=cat_view' . '&amp;gid=' . $gid . '&amp;dir=' . $direction . '&amp;order=' . $ordering . '&amp;Itemid=' . $Itemid;

		$returnArray = array($pageNav);
		return $returnArray;
	}

	function fetchPageTitle($id) {
		if (! $id > 0) {
			return;
		}
		// get the category ancestors
		$ancestors = & HANDOUT_Cats::getAncestors ( $id );
		// reverse the array
		$ancestors = array_reverse ( $ancestors );

		$returnArray = array($ancestors);
		return $returnArray;
	}

	function getGid() {
		$params = HandoutHelper::getMenuParams ();
		return JRequest::getInt ( 'gid', $params->get ( 'cat_id', 0 ) );
	}


	function _returnTo($task, $msg = '', $gid = '', $params = null) {
		return HANDOUT_Utils::returnTo ( $task, $msg, $gid, $params );
	}
}
?>