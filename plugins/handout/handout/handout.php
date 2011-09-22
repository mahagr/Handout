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

		
		
		if($listingid>0){
			
			 $db=& JFactory::getDBO();
         $query="select * from #__handout where mtree_id=".$listingid;
         $db->setQuery($query);
         $docs=$db->loadObjectList();
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
				$linkhtml=$linkhtml.'<br/><a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">'.$filename.'</a><br/><input type="file" name="handout_file_'.$doc->multi_file_no.'">';
			    $count++;
			}
			if($count>0)
			{
				
				$count=$count-1;
			}
			
		}
		
		$html = '<tr><td>'.$this->params->get('inputlabel','Handout File').'</td><td>'.$handhtml.'</td></tr>';
		$html.='<tr><td></td><td>'.$linkhtml.'</td></tr>';
		$html.='<tr><td></td><td><input type="button" onclick="count='.$count.';count=addRow(count);" value=" + New"></td></tr>';
         
    	
			
		}
		else{
			$html="";
		
		
		$label='<label>'.$this->params->get('inputlabel', 'Handout File').'</label>';
		
		$input='<input type="file" name="handout_file" class="'.$class.'"   id="'.$id.'">';
       
	
			if($istable){
		$label='<tr><td>'.$label.'</td>';
		$input='<td>'.$input.'</td></tr><tr><td></td><td><input type="button" onclick="count=0;count=addRow(count);" value=" + New"></td></tr>';
			}
	
	   $html=$label.$input;
	
	   
		}
//$html=$listingid."Habib";
		
		   return $html;
	}
	
	function onUpload($files,$name,$row)
	{
			define('INTEGRATE_SITE',JPATH_ROOT.DS.'components'.DS.'com_handout');
		define('INTEGRATE_ADMINISTRATOR',JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_handout');
		
		define ( 'JPATH_COMPONENT_HELPERS', INTEGRATE_SITE . DS . 'helpers' );
define ( 'JPATH_COMPONENT_AHELPERS', INTEGRATE_ADMINISTRATOR . DS . 'helpers' );

require_once INTEGRATE_ADMINISTRATOR . DS . 'handout.class.php';
require_once JPATH_COMPONENT_HELPERS . DS . 'helper.php';
require_once JPATH_COMPONENT_AHELPERS . DS . 'factory.php';
//echo INTEGRATE_SITE . DS . 'controller.php';


$handout = &HandoutFactory::getHandout ();


define ( 'C_HANDOUT_FILE', $handout->getPath ( 'classes', 'file' ) );


require_once C_HANDOUT_FILE;


$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
  		$_HANDOUT = &HandoutFactory::getHandout();
//echo var_dump($handout);
	$path = $_HANDOUT->getCfg('handoutpath');
	
//echo "<br/>".$path;
//echo COM_HANDOUT_VALIDATE_USER;
	//get file validation settings
   	if ($_HANDOUT_USER->isSpecial) {
	  		$validate = COM_HANDOUT_VALIDATE_ADMIN;
   		} else {
	 		if ($_HANDOUT->getCfg('user_all', false)) {
				$validate = COM_HANDOUT_VALIDATE_ALL ;
	  		} else {
		   		$validate = COM_HANDOUT_VALIDATE_USER;
	   		}
  		}
	$db= & JFactory::getDBO();
	$upload_ext='';
	$count=0;
	$query='select id from #__categories where name='.$db->Quote($name).' and section='.$db->Quote('mtree');
	$db->setQuery($query);
	$category=$db->loadResult();
	if(!$category)
	{
		$query='insert into #__categories set title='.$db->Quote($name).', name='.$db->Quote($name).', section='.$db->Quote('mtree').', image_position='.$db->Quote('left').', description='.$db->Quote($name.' Integration').', published='.$db->Quote('1');
		$db->setQuery($query);
		$db->query();
		$query="select id from #__categories order by id DESC limit 1";
		$db->setQuery($query);
		$category=$db->loadResult();
	}
	//echo $row->link_id;
	//echo var_dump($files);
    //exit();
	if($row->link_id>0)
	{
		
	$query="select * from #__handout  where mtree_id=".$row->link_id;
  	$db->setQuery($query);
  	$handoutdocs=$db->loadObjectList();
  	
  	foreach($handoutdocs as $doc)
  	{
  		$count++;
  		if($doc->multi_file_no>0){
  		$ext='handout_file_'.$doc->multi_file_no;
  		}else { $ext='handout_file';}
  		//echo $
  		
  		if($files[$ext]['name'])
  		
  		{//echo var_dump($files[$ext]['name']);
  		//exit();
  			$upload = new HANDOUT_FileUpload();
  		$file = $upload->uploadHTTP($files[$ext], $path, $validate);
  					
  			echo $query="update #__handout set docname=".$db->Quote($row->link_name).", docdescription=".$db->Quote($row->link_desc).", doclastupdateon=".$db->Quote($row->link_modified).", docfilename=".$db->Quote($files[$ext]['name']).", published=".$db->Quote('1')." where mtree_id=".$row->link_id.' and multi_file_no='.$doc->multi_file_no;
  		  	$db->setQuery($query);
  	        $db->query();
  		
  		}
  		
  	}

  	if($count>0)
  	$upload_ext='_'.$count;

  	
  	
	}
    echo $files['handout_file'.$upload_ext]['name'];
    echo $upload_ext;
    //exit();
	while($files['handout_file'.$upload_ext]['name']){
	
  		//upload the file
  		$upload = new HANDOUT_FileUpload();
  		$file = $upload->uploadHTTP($files['handout_file'.$upload_ext], $path, $validate);
  		
  
  
  	
  		$query="insert into #__handout set docname=".$db->Quote($row->link_name)." , docdescription=".$db->Quote($row->link_desc).", docdate_published=".$db->Quote($row->link_created).", docfilename=".$db->Quote($files['handout_file'.$upload_ext]['name']).", published=".$db->Quote('1').", docsubmittedby=".$db->Quote($row->user_id).", mtree_id=".$db->Quote($row->link_id).", docowner=".$db->Quote($row->user_id).", docmaintainedby=".$db->Quote($row->user_id).", multi_file_no=".$db->Quote($count).", catid=".$db->Quote($category);
  		
  	
  	
  	$db->setQuery($query);
  	$db->query();
  	
  	$count++;
  	$upload_ext='_'.$count;
		
	}
		return true;
	}
	
	function onDisplayMtreeListing($listingid)
	{
		
			 $db=& JFactory::getDBO();
         $query="select * from #__handout where mtree_id=".$listingid;
         $db->setQuery($query);
         $docs=$db->loadObjectList();
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
    
    function onDisplayMtreeEditListing($listingid)
    {
    	
    	 $db=& JFactory::getDBO();
         $query="select * from #__handout where mtree_id=".$listingid;
         $db->setQuery($query);
         $docs=$db->loadObjectList();
		$linkhtml="";
		if($docs)
		{
			foreach ($docs as $doc)
			{
				$filename=$doc->docfilename;
				if($doc->multi_file_no==0)
				$handhtml='<a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">'.$filename.'</a><br/><input type="file" name="handout_file">';
				else 
				$linkhtml=$linkhtml.'<br/><a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">'.$filename.'</a><br/><input type="file" name="handout_file_'.$doc->multi_file_no.'">';
			
			}
			
		}
		
		$html = '<tr><td>'.$this->params->get('inputlabel','Handout File').'</td><td>'.$handhtml.'</td></tr>';
		$html='<tr><td></td><td>'.$linkhtml.'</td>';
         return $html;     
    	
    }
    
    function onViewDiscussion()
    {
    	
    	
    }
    
    function onViewKunenaDiscussion()
    {
    	
    	
    }



}


?>