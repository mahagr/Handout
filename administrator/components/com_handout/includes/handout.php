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

include_once dirname(__FILE__) . '/handout.html.php';

$handout = &HandoutFactory::getHandout();

$task = $task;

switch ($task) {
	case 'stats':
		showStatistics();
		break;

	case 'credits' :
		showCredits();
		break;

	case 'sampledata':
		installSampleData();
		break;

	// DOClink
	case "doclink":
		require_once $handout->getPath('includes', 'doclink');
		showDoclink();
		break;

	case "doclink-listview":
		require_once $handout->getPath('includes', 'doclink');
		showListview();
		break;

	// CPanel
	case 'cpanel':
	default:
		showCPanel();
}

function showCPanel()
{
	HTML_HandoutHandout::showCPanel();
}

function showCredits()
{


	ob_start();
	include_once( JPATH_ROOT.'/administrator/components/com_handout/CHANGELOG.php' );
	$changelog = ob_get_clean();

	HTML_HandoutHandout::showCredits( $changelog );
}

function showStatistics()
{
	$database = &JFactory::getDBO();
	$query = "SELECT id, catid , docname , doccounter from #__handout " .
			// removed to fix artf7530
			// "\n WHERE docowner=-1 OR docowner=0 " .
			"\n ORDER BY doccounter DESC";
	$database->setQuery($query, 0, 50);
	$row = (array) $database->loadObjectList();
	HTML_HandoutHandout::showStatistics($row);
}

/**
 * Add sample category, file and document
 */
function installSampleData(){
	$database = &JFactory::getDBO();
	$user = &JFactory::getUser();

	$app = &JFactory::getApplication();
	$handoutdoc  = JPATH_ROOT.'/handouts';
	$img	= JPATH_ROOT.'/administrator/components/com_handout/images';
	$now = date('Y-m-d H:i:s');

	// get all super admins
	$database->setQuery("SELECT id FROM `#__users` WHERE `usertype`='Super Administrator'");
	$admins = implode(',', (array) $database->loadResultArray() );

	// add sample group
	$group = new HandoutGroups($database);
	$group->groups_name		 = JText::_('COM_HANDOUT_SAMPLE_GROUP');
	$group->groups_description  = JText::_('COM_HANDOUT_SAMPLE_GROUP_DESC');
	$group->groups_access	   = 1;
	$group->groups_members	  = $admins;
	if(!$group->store())
	{
		$app->redirect('index.php?option=com_handout', 'Error: installSampleData, $groups->store()');
	}
	$groupid = (-1 * $database->insertid()) + COM_HANDOUT_PERMIT_GROUP;

	// add sample license
	$license = new HandoutLicenses($database);
	$license->name	  = JText::_('COM_HANDOUT_SAMPLE_AGREEMENT');
	$license->license   = JText::_('COM_HANDOUT_SAMPLE_AGREEMENT_DESC');
	if(!$license->store())
	{
		$app->redirect('index.php?option=com_handout', 'Error: installSampleData, $license->store()');
	}
	$licenseid = $database->insertid();

	// add a sample file
	if ( !file_exists($handoutdoc.'/sample_file.png')) {
	   @copy($img.'/logo.png', $handoutdoc.'/'.JText::_('COM_HANDOUT_SAMPLE_FILENAME'));
	}

	// add sample category
	$category = new HandoutCategory($database);
	$category->parent_id		= 0;
	$category->title			= JText::_('COM_HANDOUT_SAMPLE_CATEGORY');
	$category->image			= 'clock.jpg';
	$category->section		  = 'com_handout';
	$category->image_position   = 'left';
	$category->description	  = JText::_('COM_HANDOUT_SAMPLE_CATEGORY_DESC');
	$category->published		= 1;
	$category->checked_out	  = 0;
	$category->checked_out_time = '0000-00-00 00:00:00';
	$category->editor		   = NULL;
	$category->ordering		 = 1;
	$category->access		   = 0;
	$category->count			= 0;
	$category->params		   = '';
	if(!$category->store())
	{
		$app->redirect('index.php?option=com_handout', 'Error: installSampleData, $category->store()');
	}
	$catid = $database->insertId();

	// add sample document
	$doc = new HandoutDocument($database);
	$doc->catid			 = $catid;
	$doc->docname			= JText::_('COM_HANDOUT_SAMPLE_DOC');
	$doc->docdescription	 = JText::_('COM_HANDOUT_SAMPLE_DOC_DESC');
	$doc->docdate_published  = $now;
	$doc->docowner		   = -1;
	$doc->docfilename		= JText::_('COM_HANDOUT_SAMPLE_FILENAME');
	$doc->published		 = 1;
	$doc->docurl			 = '';
	$doc->doccounter		 = 0;
	$doc->checked_out	   = 0;
	$doc->checked_out_time  = '0000-00-00 00:00:00';
	$doc->docthumbnail	   = '';
	$doc->doclastupdateon	= $now;
	$doc->doclastupdateby	= $user->id;
	$doc->docsubmittedby	  = $user->id;
	$doc->docmaintainedby	 = $groupid;
	$doc->doclicense_id	  = $licenseid;
	$doc->doclicense_display = 1;
	$doc->docversion		= '';
	$doc->doclanguage		= '';
	$doc->doc_meta_keywords	= '';
	$doc->doc_meta_description	= '';
	$doc->kunena_discuss_id	 = 0;
	$doc->access			= 0;
	$doc->attribs		   = 'crc_checksum=\nmd5_checksum=';
	if(!$doc->store())
	{
		$app->redirect('index.php?option=com_handout', 'Error: installSampleData, $doc->store()');
	}

	$app->redirect('index.php?option=com_handout', JText::_('COM_HANDOUT_SAMPLE_COMPLETED'));
}
