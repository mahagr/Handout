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

	$app = JFactory::getApplication();
	$app->registerEvent( 'onFetchButtons', 'bot_buttons' );

	$factory = JPATH_ROOT . '/administrator/components/com_handout/helpers/factory.php';
	if(file_exists($factory)){
		require_once $factory;
	}

	function bot_buttons($params) {
		$lang = JFactory::getLanguage();
		$lang->load('plg_handout_buttons', JPATH_ADMINISTRATOR);

		$handout = &HandoutFactory::getHandout();
		$handout_user = &HandoutFactory::getHandoutUser();
		require_once $handout->getPath('classes', 'button');
		require_once $handout->getPath('classes', 'token');

		$doc		= & $params['doc'];
		$file	   = & $params['file'];
		$objDBDoc   = $doc->objDBTable;

		//set parameters
	 	$plugin =& JPluginHelper::getPlugin('handout', 'buttons');
	 	$pluginParams = new JParameter( $plugin->params );

		$pluginParams->set('download', $handout->getCfg('buttons_download', '1'));
		$pluginParams->set('view', $handout->getCfg('buttons_view', '1'));
		$pluginParams->set('details', $handout->getCfg('buttons_details', '1'));
		$pluginParams->set('edit', $handout->getCfg('buttons_edit', '1'));
		$pluginParams->set('move', $handout->getCfg('buttons_move', '1'));
		$pluginParams->set('delete', $handout->getCfg('buttons_delete', '1'));
		$pluginParams->set('update', $handout->getCfg('buttons_update', '1'));
		$pluginParams->set('reset', $handout->getCfg('buttons_reset', '1'));

		$js = "javascript:if(confirm('".JText::_('PLG_HANDOUT_STANDARD_BTNS_ARE_YOU_SURE')."')) {window.location='%s'}";

		// format document links, ONLY those the user can perform.
		$buttons = array();

		if ($handout_user->canDownload($objDBDoc) &&  $pluginParams->get('download', 1)) {
			$buttons['download'] = new HANDOUT_Button('download', JText::_('PLG_HANDOUT_STANDARD_BTNS_DOWNLOAD'), $doc->_formatLink('doc_download'));
		}

		if ($handout_user->canDownload($objDBDoc) &&  $pluginParams->get('view', 1)) {
			$viewtypes = trim($handout->getCfg('viewtypes'));
			if ($viewtypes != '' && ($viewtypes == '*' || stristr($viewtypes, $file->ext))) {
				$link_params = array('tmpl' => 'component', 'format' => 'raw');
				$link = $doc->_formatLink('doc_view', $link_params, true);
				$params = new HandoutParameters('popup=1');
				$buttons['view'] = new HANDOUT_Button('view', JText::_('PLG_HANDOUT_STANDARD_BTNS_VIEW'), $link, $params);
			}
		}

		if($pluginParams->get('details', 1)) {
			$buttons['details'] = new HANDOUT_Button('details', JText::_('PLG_HANDOUT_STANDARD_BTNS_DETAILS'), $doc->_formatLink('doc_details'));
		}

		if ($handout_user->canEdit($objDBDoc) &&  $pluginParams->get('edit', 1)) {
			$buttons['edit'] = new HANDOUT_Button('edit', JText::_('PLG_HANDOUT_STANDARD_BTNS_EDIT'), $doc->_formatLink('doc_edit'));
		}

		if ($handout_user->canMove($objDBDoc) &&  $pluginParams->get('move', 1)) {
			$buttons['move'] = new HANDOUT_Button('move', JText::_('PLG_HANDOUT_STANDARD_BTNS_MOVE'), $doc->_formatLink('doc_move'));
		}

		if ($handout_user->canDelete($objDBDoc) &&  $pluginParams->get('delete', 1)) {
			$link = $doc->_formatLink('doc_delete', null, null, true);
			$buttons['delete'] = new HANDOUT_Button('delete', JText::_('PLG_HANDOUT_STANDARD_BTNS_DELETE'), sprintf($js, $link));
		}

		if ($handout_user->canUpdate($objDBDoc) &&  $pluginParams->get('update', 1)) {
			$buttons['update'] = new HANDOUT_Button('update', JText::_('PLG_HANDOUT_STANDARD_BTNS_UPDATE'), $doc->_formatLink('doc_update'));
		}

		if ($handout_user->canReset($objDBDoc) &&  $pluginParams->get('reset', 1)) {
			$buttons['reset'] = new HANDOUT_Button('reset', JText::_('PLG_HANDOUT_STANDARD_BTNS_RESET'), sprintf($js, $doc->_formatLink('doc_reset')));
		}

		if ($handout_user->canCheckin($objDBDoc) && $objDBDoc->checked_out &&  $pluginParams->get('checkout', 1)) {
			$params = new HandoutParameters('class=checkin');
			$buttons['checkin'] = new HANDOUT_Button('checkin', JText::_('PLG_HANDOUT_STANDARD_BTNS_CHECKIN'), $doc->_formatLink('doc_checkin'), $params);
		}

		if ($handout_user->canCheckout($objDBDoc) && !$objDBDoc->checked_out &&  $pluginParams->get('checkout', 1)) {
			$buttons['checkout'] = new HANDOUT_Button('checkout', JText::_('PLG_HANDOUT_STANDARD_BTNS_CHECKOUT'), $doc->_formatLink('doc_checkout'));
		}

		if ($handout_user->canPublish($objDBDoc) && !$objDBDoc->published &&  $pluginParams->get('publish', 1)) {
			$params = new HandoutParameters('class=publish');
			$link   = $doc->_formatLink('doc_publish', null, null, true);
			$buttons['publish'] = new HANDOUT_Button('publish', JText::_('PLG_HANDOUT_STANDARD_BTNS_PUBLISH'), $link, $params);
		}

		if ($handout_user->canUnPublish($objDBDoc) && $objDBDoc->published &&  $pluginParams->get('publish', 1)) {
			$link   = $doc->_formatLink('doc_unpublish', null, null, true);
			$buttons['unpublish'] = new HANDOUT_Button('unpublish', JText::_('PLG_HANDOUT_STANDARD_BTNS_UNPUBLISH'), $link);
		}

		return $buttons;

	}
?>