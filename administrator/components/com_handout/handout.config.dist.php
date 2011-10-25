<?php
if(defined('_HandoutConfig')) {
return true;
} else { 
define('_HandoutConfig',1); 

class HandoutConfig extends JObject
{
// Last Edit: ti, 2011-loka  -25 10:24
// Edited by: admin
var $HANDOUT_version = '1.0';
var $allow_bulk_download = '0';
var $author_can = '2';
var $boxchecked = '0';
var $buttons_checkout = '1';
var $buttons_delete = '1';
var $buttons_details = '1';
var $buttons_download = '1';
var $buttons_edit = '1';
var $buttons_move = '1';
var $buttons_publish = '1';
var $buttons_reset = '1';
var $buttons_update = '1';
var $buttons_view = '1';
var $cat_empty = '1';
var $cat_empty_notice = '1';
var $cat_image = '1';
var $days_for_new = '6';
var $default_editor = '0';
var $default_order = 'name';
var $default_order2 = 'DESC';
var $default_viewer = '-1';
var $details_crc_checksum = '1';
var $details_created = '1';
var $details_description = '1';
var $details_downloads = '1';
var $details_filelanguage = '1';
var $details_filename = '1';
var $details_filesize = '1';
var $details_filetype = '1';
var $details_fileversion = '1';
var $details_homepage = '1';
var $details_image = '1';
var $details_maintainers = '1';
var $details_md5_checksum = '1';
var $details_name = '1';
var $details_readers = '1';
var $details_submitter = '1';
var $details_updated = '1';
var $display_license = '0';
var $doc_icon_size = '32';
var $doc_image = '1';
var $editor_assign = '0';
var $emailgroups = '0';
var $extensions = 'zip|rar|pdf|txt';
var $fname_blank = '0';
var $fname_lc = '0';
var $fname_reject = '';
var $ga_code = '';
var $handoutpath = '/var/www-kunena/kunena16/handouts';
var $hide_remote = '0';
var $hot = '100';
var $individual_perm = '0';
var $isDown = '0';
var $item_date = '1';
var $item_description = '1';
var $item_filesize = '1';
var $item_filetype = '1';
var $item_hits = '1';
var $item_homepage = '1';
var $item_title_link = '1';
var $item_tooltip = '1';
var $log = '0';
var $maxAllowed = '1024000';
var $menu_home = '1';
var $menu_search = '1';
var $menu_upload = '1';
var $methods = array (
  0 => 'http',
);
var $notify_ondownload = '0';
var $notify_onedit = '0';
var $notify_onedit_admin = '0';
var $notify_onupload = '0';
var $notify_sendto = '';
var $overwrite = '0';
var $perpage = '5';
var $process_bots = '0';
var $reader_assign = '0';
var $registered = '2';
var $security_allowed_hosts = 'kunena16';
var $security_anti_leech = '0';
var $serverfolder = '';
var $show_share = '1';
var $show_share_compact = '1';
var $show_share_email = '1';
var $show_share_facebook = '1';
var $show_share_googleplusone = '1';
var $show_share_twitter = '1';
var $thumbs_bgcolor = 'FFFFFF';
var $thumbs_extensions = '';
var $thumbs_grayscale = '0';
var $thumbs_height = '64';
var $thumbs_jpeg_quality = '75';
var $thumbs_output_format = 'png';
var $thumbs_width = '64';
var $toolbar_icon_size = '32';
var $trimwhitespace = '0';
var $user_all = '0';
var $user_publish = '0';
var $user_upload = '0';
var $viewtypes = 'pdf|doc|txt|jpg|jpeg|gif|png';
}
}