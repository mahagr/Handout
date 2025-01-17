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

defined('_JEXEC') or die;

jimport('joomla.application.component.model');



class HandoutModelUpload extends JModel
{
	function __construct()
	{

		$handout = &HandoutFactory::getHandout ();

        require_once $handout->getPath ( 'classes', 'plugins' );
        require_once $handout->getPath ( 'classes', 'file' );

		parent::__construct();
	}

function fetchDocumentUploadForm($uid, $step, $method, $update) {
		$handout_user = &HandoutFactory::getHandoutUser();

		//preform permission check

		if ($handout_user->canPreformTask ( null, 'Upload' )) {
			HandoutHelper::_returnTo ( '', JText::_('COM_HANDOUT_NOLOG_UPLOAD') );
		}
		//check to see if method is available

		if (! $this->methodAvailable ( $method )) {
			HandoutHelper::_returnTo ( 'doc_details', JText::_('COM_HANDOUT_UPLOADMETHOD'), array ('step' => 1 ) );
		}
		switch ($step) {
			case '1' :
				return $this->fetchMethodsForm ( $uid, $step, $method );
				break;
			case '2' :
			case '3' :
				return $this->fetchMethodForm ( $uid, $step, $method, $update );
				break;

			default :
				break;
		}
	}

	function fetchMethodsForm($uid, $step, $method) {
		$task = JRequest::getCmd('task');

		// Prompt with a list of upload methods

		$lists = array ();
		$lists ['methods'] = HandoutHTML::uploadSelectList ();
		$lists ['action'] = HandoutHelper::_taskLink ( $task, $uid, array ('step' => $step + 1 ), false );

		return $lists;
	}

	function fetchMethodForm($uid, $step, $method, $update) {
		$handout = &HandoutFactory::getHandout();
		$task = JRequest::getCmd('task');
		$method_file = $handout->getPath ( 'helpers', 'upload.' . $method );
		if (! file_exists ( $method_file )) {
			HandoutHelper::_returnTo ( $task, "Protocol " . $method . " not supported", '', array ('step' => 1 ) );
		}
		require_once $method_file;

		return HandoutUploadMethod::fetchMethodForm ( $uid, $step, $update );
	}

	function methodAvailable($method) {
		$handout = &HandoutFactory::getHandout();
		$handout_user = &HandoutFactory::getHandoutUser();

		if ($handout_user->isSpecial || is_null ( $method )) {
			return true;
		}

		$methods = $handout->getCfg ( 'methods', array ('http' ) );
		if (! in_array ( $method, $methods )) {
			return false;
		}
		return true;
	}




}






?>