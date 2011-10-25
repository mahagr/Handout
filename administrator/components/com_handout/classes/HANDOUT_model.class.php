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

if (defined('_HANDOUT_MODEL')) {
	return;
}
else {
	define('_HANDOUT_MODEL', 1);
}

$handout = &HandoutFactory::getHandout();
require_once $handout->getPath('classes', 'utils');
require_once $handout->getPath('classes', 'user');

class HANDOUT_Model
{
	var $objDBTable = null;

	var $objFormatData = null;
	var $objFormatLink = null;
	var $objFormatPath = null;

	function HANDOUT_Model()
	{
		$this->objFormatData = new stdClass();
		$this->objFormatLink = new stdClass();
		$this->objFormatPath = new stdClass();
	}

	function getLink($identifier)
	{
		if (isset($this->objFormatLink->$identifier))
			return $this->objFormatLink->$identifier;
		else
			return null;
	}

	function getPath($identifier)
	{
		if (isset($this->objFormatPath->$identifier))
			return $this->objFormatPath->$identifier;
		else
			return null;
	}

	function getData($identifier)
	{
		if (isset($this->objFormatData->$identifier))
			return $this->objFormatData->$identifier;
		else
			return null;
	}

	function setData($identifier, $data)
	{
		$this->objFormatData->$identifier = $data;
	}

	function &getLinkObject()
	{
		return $this->objFormatLink;
	}

	function &getPathObject()
	{
		return $this->objFormatPath;
	}

	function &getDataObject()
	{
		return $this->objFormatData;
	}

	function getDBObject()
	{
		return $this->objDBTable;
	}

	function _format($objDBTable)
	{
	}

	function _formatLink($task, $params = array(), $sef = true, $token = false)
	{
		$handout = &HandoutFactory::getHandout();
		require_once $handout->getPath('classes', 'token');

		if ($token) {
			$params[HANDOUT_token::get(false)] = 1;
		}

		$link = HANDOUT_Utils::taskLink($task, $this->objDBTable->id, $params, $sef);
		return $link;
	}
}

class HANDOUT_Category extends HANDOUT_Model
{
	function HANDOUT_Category($id)
	{
		$this->objDBTable = &HandoutCategory::getInstance2($id);

		$this->_format($this->objDBTable);
	}

	function getPath($identifier, $type = 1, $param = null, $png = 1)
	{
		$result = null;

		switch ($identifier) {
			case 'icon':
				$result = HANDOUT_Utils::pathIcon('folder.png', $type, $param, $png);
				break;

			default:
				$result = parent::getPath($identifier);
		}

		return $result;
	}

	function _format(&$objDBCat)
	{
		$handout = &HandoutFactory::getHandout();

		$user = $handout->getUser();
		// format category data
		$this->objFormatData = HANDOUT_Utils::get_object_vars($objDBCat);

		$this->objFormatData->files = HANDOUT_Cats::countDocsInCatByUser($objDBCat->id, $user, true);
		// format category links
		$this->objFormatLink->view = $this->_formatLink('cat_view');
		// format category paths
		$this->objFormatPath->thumb = HANDOUT_Utils::pathThumb($objDBCat->image, 'images/stories/');
		$this->objFormatPath->icon = HANDOUT_Utils::pathIcon('folder.png', 1);
	}
}

class HANDOUT_Document extends HANDOUT_Model
{
	function HANDOUT_Document($id)
	{
		$database = &JFactory::getDBO();
		$this->objDBTable = new HandoutDocument($database);

		$this->objDBTable->load($id);

		$this->_format($this->objDBTable);

	}

	function &getInstance($id)
	{
		static $instances = null;

		if (!isset($instances)) {
			$instances = array();
		}

		if (!isset($instances[$id])) {
			$instances[$id] = new HANDOUT_Document($id);
		}

		return $instances[$id];
	}

	function getPath($identifier, $type = 1, $size = null)
	{
		$result = null;

		switch ($identifier) {
			case 'icon':
				$result = HANDOUT_Utils::pathIcon ($this->objFormatData->filetype . ".png", $type, $size);
				//$result = COM_HANDOUT_IMAGESPATH . 'icons/icon-' . $size . '-' . $this->objFormatData->filetype . ".png";
				break;

			default:
				$result = parent::getPath($identifier);
		}

		return $result;
	}

	function _format(&$objDBDoc)
	{
		$handout = &HandoutFactory::getHandout();
		require_once $handout->getPath('classes', 'file');
		require_once $handout->getPath('classes', 'params');
		require_once $handout->getPath('classes', 'plugins');

		$file = new HANDOUT_file($objDBDoc->docfilename, $handout->getCfg('handoutpath'));
		$params = new HandoutParametersHandler($objDBDoc->attribs, '', 'params');

		// format document data
		$this->objFormatData = HANDOUT_Utils::get_object_vars($objDBDoc);

		$this->objFormatData->owner = $this->_formatUserName($objDBDoc->docowner);
		$this->objFormatData->submitted_by = $this->_formatUserName($objDBDoc->docsubmittedby);
		$this->objFormatData->maintainedby = $this->_formatUserName($objDBDoc->docmaintainedby);
		$this->objFormatData->lastupdatedby = $this->_formatUserName($objDBDoc->doclastupdateby);
		$this->objFormatData->checkedoutby = $this->_formatUserName($objDBDoc->checked_out);
		$this->objFormatData->filename = $this->_formatFilename($objDBDoc);
		$this->objFormatData->filesize = $file->getSize();
		$this->objFormatData->filetype = $file->ext;
		$this->objFormatData->mime = $file->mime;
		$this->objFormatData->hot = $this->_formatHot($objDBDoc);
		$this->objFormatData->new = $this->_formatNew($objDBDoc);
		$this->objFormatData->params = $params;
		$this->objFormatData->docdescription = JFilterOutput::cleanText($objDBDoc->docdescription);
		$this->objFormatData->docversion = JFilterOutput::cleanText($objDBDoc->docversion);
		$this->objFormatData->doclanguage = JFilterOutput::cleanText($objDBDoc->doclanguage);
		$this->objFormatData->doc_meta_keywords = JFilterOutput::cleanText($objDBDoc->doc_meta_keywords);
		$this->objFormatData->doc_meta_description = JFilterOutput::cleanText($objDBDoc->doc_meta_description);
		$this->objFormatData->kunena_discuss_id = $objDBDoc->kunena_discuss_id;
		$this->objFormatData->mtree_id = $objDBDoc->mtree_id;

		// onFetchButtons event
		// plugins should always return an array of Button objects
		$bot = new HANDOUT_plugin('onFetchButtons');
		$bot->setParm('doc', $this);
		$bot->setParm('file', $file);
		$bot->trigger();
		if ($bot->getError()) {
			HandoutHelper::_returnTo('cat_view', $bot->getErrorMsg());
		}

		$buttons = array();
		foreach ($bot->getReturn() as $return) {
			if (!is_array($return)) {
				$return = array($return);
			}
			$buttons = array_merge($buttons, $return);
		}

		$this->objFormatLink = &$buttons;

		// format document paths
		$this->objFormatPath->icon = HANDOUT_Utils::pathIcon($file->ext . ".png", 1);
		$this->objFormatPath->thumb = HANDOUT_Utils::pathThumb($objDBDoc->docthumbnail);
	}

	//  @desc Translate the numeric ID to a character string
	//  @param integer $ The numeric ID of the user
	//  @return string Contains the user name in string format
	function _formatUserName($userid)
	{
		$handout = &HandoutFactory::getHandout();
		require_once $handout->getPath('classes', 'user');
		require_once $handout->getPath('classes', 'groups');

		switch ($userid) {
			case '-1':
				return JText::_('COM_HANDOUT_EVERYBODY');
				break;
			case '0':
				return JText::_('COM_HANDOUT_ALL_REGISTERED');
				break;
			case COM_HANDOUT_PERMIT_PUBLISHER:
				return JText::_('COM_HANDOUT_GROUP_PUBLISHER');
				break;
			case COM_HANDOUT_PERMIT_EDITOR:
				return JText::_('COM_HANDOUT_GROUP_EDITOR');
				break;
			case COM_HANDOUT_PERMIT_AUTHOR:
				return JText::_('COM_HANDOUT_GROUP_AUTHOR');
				break;
			default:
				if ($userid > 0) {
					$user = HANDOUT_users::get($userid);
					return $user->username;
				}

				if ($userid < -5) {
					$calcgroups = (abs($userid) - 10);
					$user = HANDOUT_groups::get($calcgroups);
					return $user->groups_name;
				}
				break;
		}

		return "USER ID?";
	}

	function _formatNew(&$objDBDoc)
	{
		$handout = &HandoutFactory::getHandout();
		$days = $handout->getCfg('days_for_new');
		$result = null;

		if ($days > 0 && (HANDOUT_Utils::Daysdiff($objDBDoc->docdate_published) > ($days - 2 * $days))
				&& (HANDOUT_Utils::Daysdiff($objDBDoc->docdate_published) <= 0)) {
			$result = JText::_('COM_HANDOUT_NEW');
		}
		return $result;
	}

	function _formatHot(&$objDBDoc)
	{
		$handout = &HandoutFactory::getHandout();
		$hot = $handout->getCfg('hot');
		$result = null;

		if ($hot > 0 && $objDBDoc->doccounter >= $hot) {
			$result = JText::_('COM_HANDOUT_HOT');
		}

		return $result;
	}

	function _formatFilename(&$objDBDoc)
	{
		$handout = &HandoutFactory::getHandout();
		$handout_user = $handout->getUser();

		$filename = $objDBDoc->docfilename;
		$is_link = (substr($filename, 0, strlen(COM_HANDOUT_DOCUMENT_LINK)) == COM_HANDOUT_DOCUMENT_LINK);
		$hide_remote = $handout->getCfg('hide_remote', 1);
		$can_edit = $handout_user->canEdit($objDBDoc);

		if ($is_link AND $hide_remote AND !$can_edit) {
			// strip 'Link: '
			//$filename = ereg_replace( '^'.COM_HANDOUT_DOCUMENT_LINK, '', $filename) ;
			$filename = preg_replace('/^' . COM_HANDOUT_DOCUMENT_LINK . '/', '', $filename);

			// strip scheme (http://, ftp:// )
			//$filename = ereg_replace( '^[a-zA-Z]+://', '', $filename);
			$filename = preg_replace('/^[a-zA-Z]\+:\/\//', '', $filename);

			if (strpos($filename, '/')) { // format www.mysite.com/ or www.mysite.com/path/ or www.mysite.com/path/myfile.com
			// strip domain (www.mysite.com )
			//$filename = ereg_replace( '^(([.]?[a-zA-Z0-9_-])*)/', '/', $filename);
				$filename = preg_replace('/^(([.]?[a-zA-Z0-9_-])*)\//', '/', $filename);
				// strip path
				$filename = substr($filename, strrpos($filename, '/') + 1);
			}
			else { // format www.mysite.com (no trailing slash or path or filename)
				$filename = '';
			}

			// if there's nothing left, we mark it 'unknown'
			$filename = ($filename ? JText::_('COM_HANDOUT_LINKTO') . $filename : JText::_('COM_HANDOUT_UNKNOWN'));

		}

		return $filename;
	}
}
