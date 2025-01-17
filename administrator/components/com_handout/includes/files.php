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

include_once dirname(__FILE__) . '/files.html.php';

$handout = &HandoutFactory::getHandout();

require_once $handout->getPath('classes', 'file');
require_once $handout->getPath('classes', 'utils');

// retrieve some expected url (or form) arguments
$old_filename = JRequest::getInt( 'old_filename', 1);

$cid = $cid;
$task = $task;

switch ($task)
{
	case "new": // make a new document using the selected file
		// modify the request and go to 'documents' view
		$_REQUEST['section']		= 'documents';
		$_REQUEST['uploaded_file']  = $cid[0];
		$GLOBALS['section']		= 'documents';
		$GLOBALS['uploaded_file']  = $cid[0];
		include_once($handout -> getPath('includes', 'documents'));
		break;
	case "upload" :
		{
			$step = JRequest::getVar( 'step', 1);
			$method = JRequest::getVar( 'radiobutton', null);

			if (!$method) {
				$method = JRequest::getVar( 'method', 'http');
			}

			uploadWizard($step, $method, $old_filename);
		}
		break;
	case "delete":
	case "remove":
		removeFile($cid);
		break;
	case "update":
		uploadWizard(2, 'http', $old_filename);
		break;
	case "show" :
	default :
		showFiles();
}


function showFiles()
{
	$database = &JFactory::getDBO();
	$option = JRequest::getCmd('option');
	$section = JRequest::getCmd('section');
	$app = &JFactory::getApplication();
	$list_limit = $app->getCfg('list_limit');
	$handout = &HandoutFactory::getHandout();

	$limit	  = $app->getUserStateFromRequest("viewlistlimit", 'limit', $list_limit);
	$limitstart = $app->getUserStateFromRequest("view{$option}{$section}limitstart", 'limitstart', 0);
	$levellimit = $app->getUserStateFromRequest("view{$option}{$section}limit", 'levellimit', 10);

	$filter = $app->getUserStateFromRequest("filterarc{$option}{$section}", 'filter', 0);
	$search = $app->getUserStateFromRequest( "search{$option}{$section}", 'search', '' );

	// read directory content
	$folder = new HANDOUT_Folder($handout->getCfg('handoutpath'));
	$files = $folder->getFiles($search);

	for ($i = 0, $n = count($files);$i < $n;$i++)
	{
		$file = &$files[$i];

		$database->setQuery("SELECT COUNT(docfilename) FROM #__handout WHERE docfilename='" . $database->getEscaped($file->name) . "'");
		$result = $database->loadResult();

		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		$file->links = $result;
	}

	if ($filter == 2) {
		$files = array_filter($files, 'filterOrphans');
	}
	if ($filter == 3) {
		$files = array_filter($files, 'filterDocuments');
	}

	$total = count($files);

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	 // slice out elements based on limits
	$rows = array_slice($files, $pageNav->limitstart, $pageNav->limit);

	$filters[] = JHTML::_('select.option','0', JText::_('COM_HANDOUT_SELECT_FILE'));
	$filters[] = JHTML::_('select.option','1', JText::_('COM_HANDOUT_ALLFILES'));
	$filters[] = JHTML::_('select.option','2', JText::_('COM_HANDOUT_ORPHANS'));
	$filters[] = JHTML::_('select.option','3', JText::_('COM_HANDOUT_DOCFILES'));
	$lists['filter'] = JHTML::_('select.genericlist',$filters, 'filter',
		'class="inputbox" size="1" onchange="document.adminForm.submit( );"',
		'value', 'text', $filter);

	//$search = '';

	HTML_HandoutFiles::showFiles($rows, $lists, $search, $pageNav);
}

function removeFile($cid)
{
	HANDOUT_token::check() or die('Invalid Token');
	$app = &JFactory::getApplication();
	$database = &JFactory::getDBO();

	$handout = &HandoutFactory::getHandout();

	foreach($cid as $name) {
		$database->setQuery("SELECT COUNT(docfilename) FROM #__handout WHERE docfilename='" . $database->getEscaped($name) . "'");
		$result = $database->loadResult();

		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		if ($result != 0)
			$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_ORPHANS_LINKED'));

		$file = $handout->getCfg('handoutpath') . "/" . $name;

		if (!unlink($file)) {
			$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_ORPHANS_PROBLEM'));
		}
	}

	$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_ORPHANS_DELETED'));
}

function uploadWizard($step = 1, $method = 'http', $old_filename)
{
	$handout = &HandoutFactory::getHandout();
	$database = &JFactory::getDBO();
	$app = &JFactory::getApplication();
	switch ($step) {
		case 1:
			$lists['methods'] = HandoutHTML::uploadSelectList($method);
			HTML_HandoutFiles::uploadWizard($lists);
			break;

		case 2:
			switch ($method) {
				case 'http':
					HTML_HandoutFiles::uploadWizard_http($old_filename);
					break;
				case 'ftp':
					HTML_HandoutFiles::uploadWizard_ftp();
					break;
				case 'link':
					$app->redirect("index.php?option=com_handout&section=documents&task=new&makelink=1",JText::_('COM_HANDOUT_CREATEALINK'));
					// HTML_HandoutFiles::uploadWizard_link();
					break;
				case 'transfer':
					HTML_HandoutFiles::uploadWizard_transfer();
					break;
				default:
					$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_SELECTMETHODFIRST'));
			}
			break;
		case 3:
			HANDOUT_token::check() or die('Invalid Token');
			switch ($method) {
				case 'http':
					$path = $handout->getCfg('handoutpath');

					$upload = new HANDOUT_FileUpload();
					$hash = HANDOUT_Utils::stripslashes($_FILES);
					$file_upload = isset($hash['upload']) ? $hash['upload'] : null;
					$result = &$upload->uploadHTTP($file_upload, $path, COM_HANDOUT_VALIDATE_ADMIN);

					if (!$result) {
						$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_ERROR_UPLOADING') . " - " . $upload->_err);
					} else {
						$batch = JRequest::getVar( 'batch', null);

						if ($batch && $old_filename <> null) {
							require_once 'includes/pcl/pclzip.lib.php';

							if (!extension_loaded('zlib')) {
								$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_ZLIB_ERROR'));
							}

							$target_directory = $handout->getCfg('handoutpath');
							$zip = new PclZip($target_directory . "/" . $result->name);
							$file_to_unzip = preg_replace('/(.+)\..*$/', '$1', $target_directory . "/" . $result->name);

							if (!$zip->extract(PCLZIP_OPT_PATH, $target_directory)) {
								$app->redirect("index.php?option=com_handout&section=files", JText::_('COM_HANDOUT_UNZIP_ERROR'));
							}

							@unlink ($target_directory . "/" . $result->name);
						}

						if ($old_filename) {

							$file = $handout->getCfg('handoutpath') . "/" . $old_filename;
							@unlink($file);

							$database->setQuery("UPDATE #__handout SET docfilename='". $database->getEscaped($result->name) ."' WHERE docfilename='". $database->getEscaped($old_filename) ."'");

							if (!$database->query()) {
								echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1);</script>\n";
								exit();
							}
						}

						//HTML_HandoutFiles::uploadWizard_sucess($result, $batch, $old_filename);
						$app->redirect("index.php?option=com_handout&section=files&task=upload&step=4" . "&result=" . urlencode($result->name) . "&batch=" . (0 + $batch) . "&old_filename=" . $old_filename,
							JText::_('COM_HANDOUT_SUCCESS') . ' &quot;' . $result->name . '&quot; - ' . JText::_('COM_HANDOUT_FILEUPLOADED'));
					}
					break;

				case 'ftp': break;

				case 'link': break;

				case 'transfer':

					$url  = stripslashes(JRequest::getVar( 'url', null));
					$name = stripslashes(JRequest::getVar( 'localfile', null));
					$path = $handout->getCfg('handoutpath') . "/";

					$upload = new HANDOUT_FileUpload();
					$result = $upload->uploadURL($url, $path, COM_HANDOUT_VALIDATE_ADMIN, $name);

					if ($result) {
						// HTML_HandoutFiles::uploadWizard_sucess($result, 0, 1);
						$app->redirect("index.php?option=com_handout&section=files&task=upload&step=4" . "&result=" . urlencode($result->name) . "&batch=0&old_filename=1",
							JText::_('COM_HANDOUT_SUCCESS') . ' &quot;' . $result->name . '&quot; - ' . JText::_('COM_HANDOUT_FILEUPLOADED'));
					} else {
						$app->redirect("index.php?option=com_handout&section=files", $upload->_err);
					}
					break;
			}
			break;

		case '4':/* New step that gives us a header completion message rather than
			   "in body" completion. For uniformity
			 */
			$file = new StdClass();
			$file->name = urlencode(stripslashes(JRequest::getVar( 'result' , 'INTERNAL ERROR')));
			$batch = JRequest::getVar( 'batch' , 0);
			$old_filename = JRequest::getVar( 'old_filename' , null);

			HTML_HandoutFiles::uploadWizard_sucess($file, $batch, $old_filename, 0);
			break;
	} //End switch($step)
}

function filterOrphans($var)
{
	if ($var->links != 0) {
		return false;
	}
	return true;
}

function filterDocuments($var)
{
	if ($var->links == 0) {
		return false;
	}
	return true;
}

