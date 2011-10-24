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

/* Display the Handout menu icons
*
* This template is called when user browses Handout.
*
* General variables  :
*	$this->links (object) : path to links
*	$this->perms (object) : permission values
*	$this->conf (object) : configuration values
*/

?>
	<?php
	if( !$this->conf->get('menu_home')
		&& !$this->conf->get('menu_search')
		&& !$this->conf->get('menu_upload')
		&& !$this->perms->upload) {
			// No buttons to show
	}
	else {
		?>
		<div id="htoolbar">
			<?php if($this->conf->get('menu_home')) :?>
				<div class="hbtn-home">
					<a href="<?php echo $this->links->home;?>"><img src="<?php echo COM_HANDOUT_IMAGESPATH;?>icon-<?php echo $this->conf->get('toolbar_icon_size')?>-home.png" alt="<?php echo JText::_('COM_HANDOUT_DOWNLOADS_HOME'); ?>" /></a>
					<p><a href="<?php echo $this->links->home;?>"><?php echo JText::_('COM_HANDOUT_DOWNLOADS_HOME'); ?></a></p>
				</div>
			<?php
			endif;
			if($this->conf->get('menu_search')) :
			?>
				<div class="hbtn-search">
					<a href="<?php echo $this->links->search;?>"><img src="<?php echo COM_HANDOUT_IMAGESPATH;?>icon-<?php echo $this->conf->get('toolbar_icon_size')?>-search.png" alt="<?php echo JText::_('COM_HANDOUT_SEARCH_DOC'); ?>" /></a>
					<p><a href="<?php echo $this->links->search;?>"><?php echo JText::_('COM_HANDOUT_SEARCH_DOC'); ?></a></p>
				</div>
			<?php
			endif;
			/*
			 * Check to upload permissions and show the appropriate icon/text
			 * Values for $this->perms->upload
			 *		- true 	: the user is authorized to upload
			 *		- false : the user isn't authorized to upload
			*/
			if($this->conf->get('menu_upload') && $this->perms->upload) :
			?>
				<div class="hbtn-upload">
					<a href="<?php echo $this->links->upload;?>"><img src="<?php echo COM_HANDOUT_IMAGESPATH;?>icon-<?php echo $this->conf->get('toolbar_icon_size')?>-submit.png" alt="<?php echo JText::_('COM_HANDOUT_SUBMIT'); ?>" /></a>
					<p><a href="<?php echo $this->links->upload;?>"><?php echo JText::_('COM_HANDOUT_SUBMIT'); ?></a></p>
				</div>
			<?php endif; ?>
	</div>
	<div class="clr"></div>
	<?php
	}
	?>
