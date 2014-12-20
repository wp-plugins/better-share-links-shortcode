<?php
/*
Plugin Name: Better Share Links shortcode
Plugin URI: http://www.prometod.eu/en/convenient-way-sharing-links-wordpress/
Description: Automatic saving a screenshot of a given URL and embeding it inside the content 
Version: 1.0.0
Author: peeping4dsun
Author URI: http://prometod.eu/en/
*/


/*  Copyright 2014  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//    code for implementing text button to TinyMCE editor
//    Source:   https://www.gavick.com/blog/wordpress-tinymce-custom-buttons/
add_action('admin_head', 'better_share_link_add_my_tc_button');
function better_share_link_add_my_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
   	return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
	// check if WYSIWYG is enabled
	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "better_share_link_add_tinymce_plugin");
		add_filter('mce_buttons', 'better_share_link_register_my_tc_button');
	}
}
function better_share_link_add_tinymce_plugin($plugin_array) {
   	$plugin_array['better_share_link_tc_button'] = plugins_url( '/shortcode.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
        
   	return $plugin_array;
}
function better_share_link_register_my_tc_button($buttons) {
   array_push($buttons, "better_share_link_tc_button");
   return $buttons;
}
//    end of code for implementing text button to TinyMCE editor
//-------------------------
//    begin of actual code creating the logic of the plugin

function better_share_link_function( $atts ) {
//    common code for taking the atributes of the shortcode
            extract( shortcode_atts(
                            array(
                                    'url' => '',
                                   
                            ), $atts )
                    );
            //-------------------------
            global $post;//required to get post id, which is needed later to download the screenshot image
            $url = preg_replace('/\|/', '', $url);//regex function to remove the bars ||||| coming with the url
            // the idea of ||||| is to let the user be lazy regarding the click before pasting the URL address
            $identify = $post->ID;
            $initial_flag = get_post_meta( $identify, "$url", true ); // check if we have a data or we'll have to create it
            // as you can see the meta key is the URL itself. Thus there shouldn't be any issues regarding the uniqueness of the key.
            if($initial_flag!=='1'){// if the flag is not set, then let's get inside and try to set it to '1' 
            //------------------------- IMPORTANT
            //------------------------- replace the dashes in the line below with the value of your free API key from page2images.com
            $api_data_page2images = 'http://api.page2images.com/restfullink?p2i_url='.$url.'&p2i_key=----------------&p2i_size=600x300';
            //If you want to change the image size, just replace 600x300 with your desired values
            $json = file_get_contents($api_data_page2images);
            $json_data = (json_decode($json, true));
            $success_img_url = $json_data['status'];// Getting the screenshot, is not always successful, so this value helps a lot.
            if($success_img_url==='finished') {// If there the screenshot is not ready, then better wait till it is done
            // if unsuccessfull, the code will jump to return a standart output of a link.
            // It will do that until the status changes to 'finished
            $screen_img_url = $json_data['image_url'];// the url that page2images provide for the the screenshot
            //-------------------------
            include 'simple_html_dom.php';// include the script that will deliver the URL's title
            $html = file_get_html("$url");
            if($html!==false){
            foreach($html->find('title') as $element){ 
            $external_title[] =  $element->plaintext;
            }
            //-------------------------
            if(count($external_title)===0){$external_title[0]=$url;}// if there is no title, then better set the title to the URL itself, than having no title at all
            $sanitized_title = sanitize_file_name($external_title[0]);// remove the whitespace from the title, for setting the filename of the screenshot
            $get_data_for_image_api_url = send_img_link_to_post($screen_img_url, $identify, $sanitized_title);// exchanging data with the supporting function
            $image_api_url = $get_data_for_image_api_url[1];// exchanging data with the supporting function
            add_post_meta($identify, $url, '1' , true); // Success! Let's set the meta value to '1'
            $setting_size_image = wp_get_attachment_image_src($get_data_for_image_api_url[0], 'full');// getting data about the image, later using it as image's dimensions 
            $what_the_shortcode_do = "<h3><a href ='$url' target='_blank' rel='nofollow'>$external_title[0]</a></h3><h6><a href ='$url' target='_blank' rel='nofollow'>$url</a></h6><a href ='$url' target='_blank' rel='nofollow'><img src='$image_api_url' title='$external_title[0]' alt='$external_title[0]' width='$setting_size_image[1]' height='$setting_size_image[2]'></a>";// Setting the output of the shortcode
            add_post_meta($identify, $url.'_data', "$what_the_shortcode_do" , true); // Saving the value 
            return $what_the_shortcode_do;
           }else{
            return "<a href ='$url' target='_blank' rel='nofollow'>$url</a>";}
           }
            else{
            return "<a href ='$url' target='_blank' rel='nofollow'>$url</a>";}
//-------------------------
           }
            else{// the code is executed when the  meta value corresponding to the URL is equal to '1'
              // i.e. this is what is send to the content, once the screenshot is generated
              $what_the_shortcode_do = get_post_meta( $identify, $url.'_data', true );
              return $what_the_shortcode_do;
           }
}
add_shortcode( 'better_share_link', 'better_share_link_function' );
//-------------------------
      function send_img_link_to_post($url, $identify, $new_file_name){
$get_the_id_new_image = screen_somatic_attach_external_image($url, $identify, false, $new_file_name );// sending data to the function that actually executes the saving of the file
// the returning value is the id of the attachment file
$get_the_url_new_image[0] = $get_the_id_new_image; // saving the attachment ID
$get_the_url_new_image[1] = wp_get_attachment_url( $get_the_id_new_image );// getting the URL of the attachment file
return $get_the_url_new_image;
        }
        //-------------------------
        //Below is the function that saves the file
        //  Source:  http://wordpress.stackexchange.com/questions/30284/media-sideload-image-file-name/
        //  Author: http://wordpress.stackexchange.com/users/838/somatic

        /**
 * Download an image from the specified URL and attach it to a post.
 * Modified version of core function media_sideload_image() in /wp-admin/includes/media.php  (which returns an html img tag instead of attachment ID)
 * Additional functionality: ability override actual filename, and to pass $post_data to override values in wp_insert_attachment (original only allowed $desc)
 *
 * @since 1.4 Somatic Framework
 *
 * @param string $url (required) The URL of the image to download
 * @param int $post_id (required) The post ID the media is to be associated with
 * @param bool $thumb (optional) Whether to make this attachment the Featured Image for the post (post_thumbnail)
 * @param string $filename (optional) Replacement filename for the URL filename (do not include extension)
 * @param array $post_data (optional) Array of key => values for wp_posts table (ex: 'post_title' => 'foobar', 'post_status' => 'draft')
 * @return int|object The ID of the attachment or a WP_Error on failure
 */
function screen_somatic_attach_external_image( $url = null, $post_id = null, $thumb = null, $filename = null, $post_data = array() ) {
    if ( !$url || !$post_id ) return new WP_Error('missing', "Need a valid URL and post ID...");
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp
    $tmp = download_url( $url );

    // If error storing temporarily, unlink
    if ( is_wp_error( $tmp ) ) {
        @unlink($file_array['tmp_name']);   // clean up
        $file_array['tmp_name'] = '';
        return $tmp; // output wp_error
    }

    preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);    // fix file filename for query strings
    $url_filename = basename($matches[0]);                                                  // extract filename from url for title
    $url_type = wp_check_filetype($url_filename);                                           // determine file type (ext and mime/type)

    // override filename if given, reconstruct server path
    if ( !empty( $filename ) ) {
        $filename = sanitize_file_name($filename);
        $tmppath = pathinfo( $tmp );                                                        // extract path parts
        $new = $tmppath['dirname'] . "/". $filename . "." . $tmppath['extension'];          // build new path
        rename($tmp, $new);                                                                 // renames temp file on server
        $tmp = $new;                                                                        // push new filename (in path) to be used in file array later
    }

    // assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
    $file_array['tmp_name'] = $tmp;                                                         // full server path to temp file

    if ( !empty( $filename ) ) {
        $file_array['name'] = $filename . "." . $url_type['ext'];                           // user given filename for title, add original URL extension
    } else {
        $file_array['name'] = $url_filename;                                                // just use original URL filename
    }

    // set additional wp_posts columns
    if ( empty( $post_data['post_title'] ) ) {
        $post_data['post_title'] = basename($url_filename, "." . $url_type['ext']);         // just use the original filename (no extension)
    }

    // make sure gets tied to parent
    if ( empty( $post_data['post_parent'] ) ) {
        $post_data['post_parent'] = $post_id;
    }

    // required libraries for media_handle_sideload
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // do the validation and storage stuff
    $att_id = media_handle_sideload( $file_array, $post_id, null, $post_data );             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status

    // If error storing permanently, unlink
    if ( is_wp_error($att_id) ) {
        @unlink($file_array['tmp_name']);   // clean up
        return $att_id; // output wp_error
    }

    // set as post thumbnail if desired
    if ($thumb) {
        set_post_thumbnail($post_id, $att_id);
    }

    return $att_id;
}
