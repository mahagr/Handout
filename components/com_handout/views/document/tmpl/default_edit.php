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

/* Display the edit form page(required)
*
* This template is called when user preforms the Edit operation on a document.
*
* General variables  :
*	$this->data (object) : configuration values
*	$this->buttons (object) : permission values
*	$this->paths (object) : configuration values
*	$this->links (object) : path to links
*	$this->permission (object) : permission values
*	$this->conf (object) : configuration values
*/

?>
<?php
	$app = &JFactory::getApplication();
	$app->setPageTitle(JText::_('COM_HANDOUT_TITLE_EDIT') . ' | ' . $this->data->docname);
	$pathway = & $app->getPathWay();
	$pathway->addItem($this->data->docname);
	$pathway->addItem('Edit');

	JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);
	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.calendar');
?>
<div id="handout" class="hedit">
	<?php $this->_addPath( 'template', JPATH_COMPONENT . '/views/handout/tmpl' );?>
	<?php echo $this->loadTemplate('menu'); ?>

	<h2><?php echo JText::_('COM_HANDOUT_TITLE_EDIT');?></h2>

	<!-- <ul>
		<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><span><span><?php echo JText::_('COM_HANDOUT_CANCEL')?></span></span></a></li>
		<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><span><span><?php echo JText::_('COM_HANDOUT_SAVE')?></span></span></a></li>
	</ul>-->

	<?php echo editDocumentForm ( $this->edit_doc, $this->edit_lists, $this->edit_last, $this->edit_created, $this->edit_params ); ?>

	<!-- <ul>
		<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><span><span><?php echo JText::_('COM_HANDOUT_CANCEL')?></span></span></a></li>
		<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><span><span><?php echo JText::_('COM_HANDOUT_SAVE')?></span></span></a></li>
	</ul>-->

	<div class="clr"></div>

	<script language="javascript" type="text/javascript">
	<!--
		list = document.getElementById('docthumbnail');
		img  = document.getElementById('docthumbnail_preview');
		list.onchange = function() {
			var index = list.selectedIndex;
			if(list.options[index].value!='') {
				img.src = 'images/stories/' + list.options[index].value;
			} else {
				img.src = 'images/blank.png';
			}
		}
	//-->
	</script>
	<?php include_once(JPATH_COMPONENT . '/footer.php'); ?>
</div>

<?php

	function editDocumentForm(&$row, &$lists, $last, $created, &$params)
	{
		$Itemid = JRequest::getInt('Itemid');

		JFilterOutput::objectHTMLSafe( $row );

		ob_start();
		?>
		<form action="index.php" method="post" name="adminForm" onsubmit="javascript:setgood();" id="hform-edit" class="hform">



			<ul>
						<li><input type="button" name="cancel" onclick="submitbutton('cancel');" value="<?php echo JText::_('COM_HANDOUT_CANCEL');?>"></li>
						<li><input type="submit" name="sub" value="<?php echo JText::_('COM_HANDOUT_SAVE');?>"></li>
					</ul>
		<?php

			$tabs = new HandoutTabs(0);
			echo $tabs->startPane("content-pane");
			echo $tabs->startPanel(JText::_('COM_HANDOUT_DOC'), "document-page");

			_showTabDocument($row, $lists, $last, $created);

			echo $tabs->endPanel();
			echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_PERMISSIONS'), "permissions-page");

			_showTabPermissions($row, $lists, $last, $created);

			echo $tabs->endPanel();
			echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_AGREEMENT'), "license-page");

			_showTabLicense($row, $lists, $last, $created);

			if(isset($params)) :
			echo $tabs->endPanel();
			echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_DETAILS'), "details-page");

			_showTabDetails($row, $lists, $last, $created, $params);
			endif;

			echo $tabs->endPanel();
			echo $tabs->endPane();
			?>
			<br />
			<p>
				<label for="hform-description"><?php echo JText::_('COM_HANDOUT_DESCRIPTION');?></label><br />
				<?php
					jimport( 'joomla.html.editor' );
					$editor =& JFactory::getEditor();
					echo $editor->display('docdescription', $row->docdescription , '550', '250', '50', '10') ;
				?>
			</p>

			<input type="hidden" name="goodexit" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="task" value="doc_save" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="doccounter" value="<?php echo $row->doccounter;?>" />
			<input type="hidden" name="doclastupdateon" value="<?php echo date('Y-m-d H:i:s') ?>" />
			<?php echo HANDOUT_token::render();?>


			<ul>
						<li><input type="button" name="cancel" onclick="submitbutton('cancel');" value="<?php echo JText::_('COM_HANDOUT_CANCEL');?>"></li>
						<li><input type="submit" name="sub"   value="<?php echo JText::_('COM_HANDOUT_SAVE');?>"></li>
					</ul>
		</form>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function _showTabDocument(&$row, &$lists, $last, $created)
	{
		$handout_user = &HandoutFactory::getHandoutUser();
		JHTML::_('behavior.calendar');
		?>
		<table class="adminform">
			<tbody>
				<tr>
					<td>
						<label for="hform-docname"><?php echo JText::_('COM_HANDOUT_TITLE');?></label><br />
						<input class="inputbox" type="text" name="docname" size="50" maxlength="100" value="<?php echo $row->docname;?>" />

						<p>
							<label for="hform-catid"><?php echo JText::_('COM_HANDOUT_CAT');?></label><br />
							<?php echo $lists['catid'];?>
						</p>
						<p>
							<label for="hform-publish"><?php echo JText::_('COM_HANDOUT_DATE');?></label><br />
							<?php echo JHTML::_('calendar', $row->docdate_published, 'docdate_published', 'docdate_published', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
						</p>

						<p>
							<label for="hform-thumb"><?php echo JText::_('COM_HANDOUT_THUMBNAIL');?></label><br />
							<?php echo $lists['docthumbnail'];?>
							<?php $previewfull = $lists['docthumbnail_preview'] ? "images/stories/".$lists['docthumbnail_preview'] : "images/M_images/blank.png";?>
							<img src="<?php echo $previewfull ?> " id="docthumbnail_preview" alt="Preview" />
						</p>
						<p>
							<label for="hform-filename"><?php echo JText::_('COM_HANDOUT_FILE');?></label><br />
							<?php echo $lists['docfilename'];?>
						</p>

						<?php
						if (isset($row->docfilename)) :
						?>
							<p>
								<label for="hform-filename"><?php echo JText::_('COM_HANDOUT_DOCURL');?></label><br />
								<input class="inputbox" type="text" name="document_url" size="50" maxlength="200" value="<?php echo $row->docfilename ?>" />

								<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_DOCURL');?>::<?php echo JText::_('COM_HANDOUT_DOCURL_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

							</p>
						<?php
						endif;
						?>
						<p>
							<label for="hform-url"><?php echo JText::_('COM_HANDOUT_INFOURL');?></label><br />
							<input class="inputbox" type="text" id="hform-url" size="50" maxlength="200" value="<?php echo $row->docurl ?>" />

							<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_INFOURL');?>::<?php echo JText::_('COM_HANDOUT_INFOURL_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

							<div><em>(<?php echo JText::_('COM_HANDOUT_MAKE_SURE');?>)</em></div>
						</p>
						<?php
						if ($handout_user->canPublish()) : ?>
						<p>
							<label><?php echo JText::_('COM_HANDOUT_PUBLISHED');?></label><br />
							<?php echo $lists['published']; ?>
						</p>
						<?php
						endif;
						?>
						 <p>
								<label for="hform-filename"><?php echo JText::_('DOWNLOAD_LIMIT');?></label><br />
								<input class="inputbox" type="text" name="download_limit" size="15" maxlength="200" value="<?php echo $row->download_limit; ?>" />

								<span class="hasTip" title="<?php echo JText::_('DOWNLOAD_LIMIT');?>::<?php echo JText::_('DOWNLOAD_LIMIT_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

		                </p>
						<p>
							<input type="checkbox" <?php if($row->allow_single_download==1)echo 'checked="checked"';?> name="allow_single_download"  value="1"/> <?php echo JText::_('ALLOW_SINGLE_DOWNLOAD');?>


							<span class="hasTip" title="<?php echo JText::_('ALLOW_SINGLE_DOWNLOAD');?>::<?php echo JText::_('ALLOW_SINGLE_DOWNLOAD_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

						</p>


					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	function _showTabPermissions(&$row, &$lists, $last, $created)
	{
		?>
		<fieldset class="input">
			<p>
				<label for="hform-owner"><?php echo JText::_('COM_HANDOUT_OWNER');?></label><br />
				<?php echo $lists['viewer'];?>

				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_OWNER');?>::<?php echo JText::_('COM_HANDOUT_OWNER_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>
				</p>
			<p>
				<label for="hform-maintainedby"><?php echo JText::_('COM_HANDOUT_MAINTAINER');?></label><br />
				<?php echo $lists['maintainer']; ?>

				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MAINTAINER');?>::<?php echo JText::_('COM_HANDOUT_MANT_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

			</p>
			<p>
				<label for="hform-createdby"> <?php echo JText::_('COM_HANDOUT_CREATED_BY_LABEL');?></label><br />
				[<?php echo $created[0]->name;?>]&nbsp;
				<em>
					<?php echo JText::_('COM_HANDOUT_ON') . "&nbsp;"; ?>
					<?php
					if ($row->docdate_published) {
						echo HandoutFactory::getFormatDate($row->docdate_published);
					} else {
						$date = date("Y-m-d H:i:s", time("Y-m-d g:i:s"));
						echo  HandoutFactory::getFormatDate($row->docdate_published);
					}
					?>
				</em>
			</p>
			<p>
				<label for="hform-updatedby"> <?php echo JText::_('COM_HANDOUT_UPDATED_BY');?></label><br />
				[<?php echo $created[0]->name;?>]&nbsp;

				<?php
				if (!strstr($row->doclastupdateon, '0000-00-00')) {
					echo "<em>" . JText::_('COM_HANDOUT_ON') . "&nbsp;" . HandoutFactory::getFormatDate($row->doclastupdateon) ."</em>" ;
				} ?>
			</p>
  		</fieldset>
  		<?php
	}

	function _showTabLicense(&$row, &$lists, $last, $created)
	{
		?>
		<fieldset class="input">
			<p>
				<label for="hform-agreement-type"><?php echo JText::_('COM_HANDOUT_AGREEMENT_TYPE');?></label><br />
				<?php echo $lists['licenses']; ?>

				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_AGREEMENT_TYPE');?>::<?php echo JText::_('COM_HANDOUT_AGREEMENT_TOOLTIP');?>">
						<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

			</p>
			<p>
				<label for="hform-agreement"><?php echo JText::_('COM_HANDOUT_DISPLAY_AGREEMENT');?></label><br />
				<?php echo $lists['licenses_display']; ?>

				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_DISPLAY_AGREEMENT');?>::<?php echo JText::_('COM_HANDOUT_DISPLAY_AGREEMENT_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>

			</p>
		</fieldset>
		<?php
	}

	function _showTabDetails(&$row, &$lists, $last, $created, &$params)
	{
		?>
		<fieldset class="input">
			<?php echo $params->render('params', 'Tableless');?>
		</fieldset>
		<?php
	}
