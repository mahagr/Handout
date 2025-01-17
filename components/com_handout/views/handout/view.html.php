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

jimport ( 'joomla.application.component.view' );



class HandoutViewHandout extends JView {
	function display() {
		 /* $type= JRequest::getVar('type','');
		  if($type!='mtree')
		  {
		  */
		$model=$this->getModel();
		$handout = &HandoutFactory::getHandout();
		$gid = HandoutHelper::getGid ();
       // echo $gid;
      //  exit();
		list($links, $perms) =HandoutHelper::fetchMenu ( $gid);

		$category = new StdClass ();
		if ($gid > 0) {
			list($category->links, $category->paths, $category->data) = $model->getCategory ( $gid);
			$this->assign('cat_empty',true );
		} else {
			$this->assign('cat_empty',false );
		}

		$cat_list = new StdClass ();
		$cat_list->items = $model->getCategoryList($gid);

		//echo var_dump($cat_list->items );


			$docmodel= & JModel::getInstance('Document','HandoutModel');
		$doc_list = new StdClass ();
		list($doc_list->order, $doc_list->items) = $docmodel->getDocumentList ( $gid);


		list($pagenav) =$model->getPageNav ( $gid );
		$pagetitle = $model->getPageTitle ( $gid);


		//echo var_dump($doc_list);
             //   exit();
		$this->assignRef('category', $category);
		$this->assignRef('cat_list', $cat_list);
		$this->assignRef('doc_list', $doc_list);
		$this->assignRef('pagenav', $pagenav);
		$this->assignRef('pagetitle', $pagetitle);
		$this->assignRef('links', $links);
		$this->assignRef('perms', $perms);
		$this->assignRef('conf', $handout->getAllCfg());
		parent::display();
		  /*}else

		  {
		  	$model=$this->getModel();




		  }*/
	}
}
?>