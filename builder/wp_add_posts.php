<?php 
 // Load WordPress
require_once '../wp-load.php';
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once ABSPATH . '/wp-admin/includes/taxonomy.php';
 // Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/London');

function categories($feed){
	$ids = array();
	$cats = explode(',', $feed);
	foreach($cats as $cat){
		$ids[] = wp_create_category($cat);		
	}
	
	return $ids;
}
function thumbnail($file, $post_id){

$file = str_replace("wp-content", "", __DIR__)."/".$file;
$filename = explode("/", $file);
$filename = array_reverse($filename);

$upload_file = wp_upload_bits( $filename[0], null, @file_get_contents( $file ) );
if ( ! $upload_file['error'] ) {
  // if succesfull insert the new file into the media library (create a new attachment post type).
  $wp_filetype = wp_check_filetype($filename[0], null );
 
  $attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_parent'    => $post_id,
    'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename[0] ),
    'post_content'   => '',
    'post_status'    => 'inherit'
  );
 
  $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
 
  if ( ! is_wp_error( $attachment_id ) ) {
     // if attachment post was successfully created, insert it as a thumbnail to the post $post_id.
     require_once(ABSPATH . "wp-admin" . '/includes/image.php');
 
     $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
 
     wp_update_attachment_metadata( $attachment_id,  $attachment_data );
     set_post_thumbnail( $post_id, $attachment_id );
   }
}
	
	return "";
}

if($_POST['pass'] == 'damnpassword123!@#'){
	
	$title = $_POST['ptitle'];
	$file = $_POST['file'];
	$tags = $_POST['tags'];
	$categories = $_POST['categories'];
	$content = $_POST['content'];
	$content = str_replace('\\\\\\', "", $content);
	$content = str_replace('\\\\r', "", $content);
	$content = str_replace('\\\\n', "", $content);
	
	preg_match('/<h1>(.+)<\/h1>/U', $content, $post_title);
	$post_title = ucwords($post_title[1]);
	
	$content = preg_replace('/<h1>(.+)<\/h1>/', '', $content);
	
	$user_id = $_POST['userid'];
	$url = $_POST['slug'];
	// Post filtering
remove_filter('content_save_pre', 'wp_filter_post_kses');
remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
 // Create post
$id = wp_insert_post(array(
    'post_title'    => $post_title,
    'post_content'  => $content,
    'post_date'     => date('Y-m-d ').rand(10,17).':'.rand(10,59).':'.rand(10,59),
    'post_author'   => $user_id,
    'post_type'     => 'post',
    'post_status'   => 'publish',
	'post_name'		=> $url,
	'meta_input'   => array(
        '_yoast_wpseo_title' => $title.' %%page%% %%sep%% %%sitename%%',
    ),
));
// Post filtering
add_filter('content_save_pre', 'wp_filter_post_kses');
add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
 if ($id) {
     // Set category - create if it doesn't exist yet
    wp_set_post_terms($id, categories($categories), 'category');
    wp_set_post_terms($id, $tags, 'post_tag', true);
	thumbnail($file, $id);
	echo "1";
     // Add meta data, if required
   //add_post_meta($id, 'meta_key', $metadata);
} else {
    echo "WARNING: Failed to insert post into WordPress";
}


}
?>