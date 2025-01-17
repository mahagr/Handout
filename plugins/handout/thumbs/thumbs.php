<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout Thumbnails
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

// requires
require_once dirname(__FILE__).'/thumbs/defines.php';

jimport( 'joomla.plugin.plugin' );
class plgHandoutThumbs extends JPlugin
{
	public function onFetchDocument($params)
	{
		require_once JPATH_ROOT . '/administrator/components/com_handout/helpers/factory.php';
		$handout = &HandoutFactory::getHandout();

		// load plugin params info
	 	$plugin =& JPluginHelper::getPlugin('handout', 'thumbs');
	 	$pluginParams = new JParameter( $plugin->params );

		//set parameters from Handout config
		$pluginParams->set('extensions', $handout->getCfg('thumbs_extensions'));
		$pluginParams->set('width', $handout->getCfg('thumbs_width', '64'));
		$pluginParams->set('height', $handout->getCfg('thumbs_height', '64'));
		$pluginParams->set('output_format', $handout->getCfg('thumbs_output_format', 'png'));
		$pluginParams->set('jpeg_quality', $handout->getCfg('thumbs_jpeg_quality', '75'));
		$pluginParams->set('background_color', $handout->getCfg('thumbs_background_color', 'FFFFFF'));
		$pluginParams->set('grayscale', $handout->getCfg('thumbs_grayscale', '0'));

		// Handout config
		$handoutpath = $handout->getCfg('handoutpath', JPATH_ROOT.'/handouts');

		// Parameters
		$id	 = $params['id'];
		$type   = $params['type'];

		// get the document instance
		$doc = & HANDOUT_Document::getInstance($id);

		// check for existing thumbnail
		if($doc->objDBTable->docthumbnail) { return; }

		// docfilename exists?
		if(!file_exists($handoutpath)) { return; }
		if(!file_exists($handoutpath.'/'.$doc->objDBTable->docfilename)) { return; }

		// check extension
		$extensions = $pluginParams->get( 'extensions', _AT_FILETYPE_LIST );
		if(!in_array(strtolower($doc->objFormatData->filetype), explode(',', $extensions))) { return; }

		// Target path writable?
		if (!file_exists(_AT_PATH_IMAGES)){
			mkdir(_AT_PATH_IMAGES);
			chmod(_AT_PATH_IMAGES, 0755);
			copy (JPATH_ROOT . '/tmp/index.html', _AT_PATH_IMAGES . '/index.html');
		}

		if(!is_writable(_AT_PATH_IMAGES)) { return; }

		// build target filename
		$output_format  = $pluginParams->get('output_format', 'png');
		$target = _AT_PATH_IMAGES.'/'.$doc->objDBTable->docfilename.'_'.$doc->objDBTable->id.'.'.$output_format;

		// phpThumb
		require_once _AT_PATH_LIBRARIES.'/phpthumb/phpthumb.class.php';
		$phpThumb  = new phpThumb();

		// parameters
		$phpThumb->setSourceFilename( $handoutpath.'/'.$doc->objDBTable->docfilename );
		$phpThumb->setParameter('w',	$pluginParams->get('width', 64));
		$phpThumb->setParameter('h',	$pluginParams->get('height', 64));
		$phpThumb->setParameter('far',  'C');
		$phpThumb->setParameter('f',	$pluginParams->get('output_format', 'png'));
		$phpThumb->setParameter('q',	$pluginParams->get('jpeg_quality', 75));
		$phpThumb->setParameter('bg',   $pluginParams->get('background_color', 'FFFFFF'));
		if ($pluginParams->get('grayscale', 0)) {
			$phpThumb->setParameter('fltr', 'gray' );
		}

		// generate
		if ( !$phpThumb->GenerateThumbnail() ) { return; }

		// render
		if (!$phpThumb->RenderToFile($target)) { return; }
		unset($phpThumb);

		// assign thumbnail
		$doc->objDBTable->docthumbnail	   = basename($target);
		$doc->objFormatData->docthumbnail	= $doc->objDBTable->docthumbnail;
		$doc->objFormatPath->thumb 			= HANDOUT_Utils::pathThumb($doc->objDBTable->docthumbnail);

		// store
		$doc->objDBTable->store();
		return;
	}
}