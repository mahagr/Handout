<?php

jimport('joomla.plugin.plugin');

JPlugin::loadLanguage( 'plg_handout_handout' );

class plgHandoutHandout extends JPlugin
{

	function plgHandoutHandout(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

	}

	function getUploadForm($class="handoutfile",$id="handoutfile",$istable,$listingid)
	{
           $progressImg = JURI::root().'/administrator/components/com_handout/images/uploader.gif';

		if($listingid>0){

			 $db=& JFactory::getDBO();
         $query="select * from #__handout where mtree_id=".$listingid;
         $db->setQuery($query);
         $docs = (array) $db->loadObjectList();
		$linkhtml="";
		$count=0;
		if($docs)
		{
			foreach ($docs as $doc)
			{
				$filename=$doc->docfilename;
				if($doc->multi_file_no==0)
				$handhtml='<a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">'.$filename.'</a><br/><input type="file" name="handout_file">';
				else
				$linkhtml=$linkhtml.'<a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">'.$filename.'</a><br/><input type="file" name="handout_file_'.$doc->multi_file_no.'"><br/>';
			    $count++;
			}
			if($count>0)
			{

				$count=$count-1;
			}

		}else
		{
				$handhtml='<input type="file" name="handout_file" class="'.$class.'"   id="'.$id.'">';

		}

		$html = '<tr><td>'.$this->params->get('inputlabel','Handout File').'</td><td>'.$handhtml.'</td><td><div id="progress" style="display:none;"><img src="'.$progressImg.'" alt="Upload Progress" />&nbsp;'.JText::_('File Uploading ...').'</div></td></tr>';
		$html.='<tr><td></td><td>'.$linkhtml.'</td></tr>';
		$html.='<tr><td></td><td><input type="button" onclick="setCount(\''.$count.'\');addRow();" value=" + New"></td></tr>';

		}
		else{
			$html="";

		$label='<label>'.$this->params->get('inputlabel', 'Handout File').'</label>';

		$input='<input type="file" name="handout_file" class="'.$class.'"   id="'.$id.'">';

			if($istable){
		$label='<tr><td>'.$label.'</td>';
		$input='<td>'.$input.'</td><td><div id="progress" style="display:none;"><img src="'.$progressImg.'" alt="Upload Progress" />&nbsp;'.JText::_('File Uploading ...').'</div></td></tr><tr><td></td><td><input type="button" onclick="addRow();" value=" + New"></td></tr>';
			}

	   $html=$label.$input;

		}
//$html=$listingid."Habib";

		   return $html;
	}

	function onUpload($files,$row)
	{

		/* Including Handout libraries to upload file through Plugin */
	   define('INTEGRATE_SITE',JPATH_ROOT.'/components/com_handout');
	   define('INTEGRATE_ADMINISTRATOR',JPATH_ROOT.'/administrator/components/com_handout');
	   define ( 'JPATH_COMPONENT_HELPERS', INTEGRATE_SITE . '/helpers' );
       define ( 'JPATH_COMPONENT_AHELPERS', INTEGRATE_ADMINISTRATOR . '/helpers' );
       require_once INTEGRATE_ADMINISTRATOR . '/handout.class.php';
       require_once JPATH_COMPONENT_HELPERS . '/helper.php';

		/* Handout Object */
       $handout = &HandoutFactory::getHandout ();

		/* Including Handout File librarry to upload file through Plugin */
       define ( 'C_HANDOUT_FILE', $handout->getPath ( 'classes', 'file' ) );
       require_once C_HANDOUT_FILE;

		/* Handout User Object */
       $handout_user = &HandoutFactory::getHandoutUser();

		/* Including Handout libraries to upload file through Plugin */
  	   $handout = &HandoutFactory::getHandout();

		/* Handout Upload Path */
	    $path = $handout->getCfg('handoutpath');

		/* Validate Handout User */
   	if ($handout_user->isSpecial) {
	  		$validate = COM_HANDOUT_VALIDATE_ADMIN;
   		} else {
	 		if ($handout->getCfg('user_all', false)) {
				$validate = COM_HANDOUT_VALIDATE_ALL ;
	  		} else {
		   		$validate = COM_HANDOUT_VALIDATE_USER;
	   		}
  		}

	$ext='';
	$count=0;
	$err=array();
		/* Document Table Object */
    $db= & JFactory::getDBO();

   	//File Object to upload File
    $upload = new HANDOUT_FileUpload();

   	 	/* if mtree link editing */

	if($row->link_id>0)
	{
		/* retrive all docs by mtree link id */
	$query="select * from #__handout  where mtree_id=".$row->link_id;
  	$db->setQuery($query);
  	$handoutdocs= (array) $db->loadObjectList();
  	$count=count($handoutdocs);
  	 foreach($handoutdocs as $doc)
  	{     //if first file have no extended number to add new file field
		if($doc->multi_file_no>0)
			$ext='handout_file_'.$doc->multi_file_no;
			else
			$ext='handout_file';

			$filename="";
			$filename= isset($files[$ext]) ? $files[$ext]['name'] : null;
			if($filename)
			{
				$error=null;
				$upload->_clearError();
				$upload->uploadHTTP($files[$ext], $path, $validate);
			 	$error=$upload->_getError();

				if(!$error)
				{  // updating handout  document
			    	$document = new HandoutDocument ( $db );
					$document->load($doc->id);
					$docname="";
					$docname=explode('.', $filename);
					$document->docname=$docname[0];
					$document->docfilename=$filename;
					$document->doclastupdateby=$row->user_id;
					$document->doclastupdateon=$row->link_modified;
					$document->store();

				}else{
					$err[]=$error;

				}

			}

  	}

	}

	// adding new file with mtree link
	foreach ($files as $file) {
		$filename = $file['name'];

			$error=null;
			$upload->_clearError();
			$upload->uploadHTTP($file, $path, $validate);
			$error=$upload->_getError();
			if(!$error)
			{

  		     	$document = new HandoutDocument ( $db );
  	            $mdoc=new stdClass();
				$docname=explode('.', $filename);
				$mdoc->docname=$docname[0];
				$mdoc->docdate_published=$row->link_created;
				$mdoc->docfilename=$filename;
				$mdoc->published=1;
				$mdoc->docsubmittedby=$row->user_id;
				$mdoc->mtree_id=$row->link_id;
				$mdoc->docowner=-1;
				$mdoc->docmaintainedby=$row->user_id;
				$mdoc->multi_file_no=$count;
				$mdoc->cattype='mtree';
				$document->bind($mdoc);
				$document->store();

			}else{

				$err[]=$error;
			}

		$count++;
	}

		return $err;
	}
	//display Handout File List in Mtree Link Details
	function onDisplayMtreeListing($listingid)
	{

		$db=& JFactory::getDBO();
         $query="select * from #__handout where mtree_id=".$listingid;
         $db->setQuery($query);
         $docs= (array) $db->loadObjectList();
		$linkhtml="";
		if($docs)
		{
			foreach ($docs as $doc)
			{
				$filename=explode('.',$doc->docfilename);
				$linkhtml=$linkhtml.'<a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">'.$filename[0].'</a><br/>';
			}

		}

		$html = '<div class="row"><div class="caption">Handout File </div><div class="data">'.$linkhtml.'</div></div>';
         return $html;
    }

}
