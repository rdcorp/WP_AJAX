<?php
/**
 * Plugin Name: AJAX Class Function
 * Plugin URI: http://www.rohanvyas.com
 * Description: This plugin will call ajax function by extending class
 * Version: 1.0
 * Author: Rohan Vyas
 * Author URI: http://www.rohanvyas.com
 * 
 * This is my custom code, which is an example of usage of the WP_AJAX class.
 * 
 */
 
require("WP_Ajax.php");

Class add_random_post extends WP_AJAX{
    protected $action = 'add_random_post';

    protected function run(){
        
        if($this->isLoggedIn()){
            
            $post = [
                'post_status' => 'publish'
            ];
            
            if( $this->requestType(['POST', 'put']) ){
                $post['post_content'] = 'This request was either POST or PUT';
            }else if( $this->requestType('get') ){
                $post['post_content'] = 'This request was GET';
            }

            $post['post_title'] = sprintf('This post was created by %s', $this->user->data->user_nicename);
            
            wp_insert_post($post);
            
            $this->JSONResponse($post);
            
        }
    }
}

add_random_post::listen();


function my_scripts_method() {
    wp_enqueue_script( 'jquery' );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

function add_random_post_shortcode() {

    $message='<script>
    jQuery(document).ready(function(){
      jQuery("#btn_add_new_post").click(function(){
       
       jQuery.post("'.get_site_url().'/wp-admin/admin-ajax.php",{
            action: "add_random_post",
        },function(data){
            console.log(data)
        }, "JSON");
       
      });
    });
    </script>';
    $message.= '<p><a id="btn_add_new_post">Click Here to add new post</a></p>';
 

    echo $message;

} 
// register shortcode
add_shortcode('add_random_post', 'add_random_post_shortcode'); 

/* Example 2 - Changes the title of the page to random string */

Class change_page_title extends WP_AJAX{
    
    protected $action = 'change_page_title';

    protected function run(){
        
        if($this->isLoggedIn()){
            
            $post_id = $_POST["page_id"];
            
            $permitted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ';
            
            $new_title = substr(str_shuffle($permitted_chars), 0, 16);
            
                $post_update = array(
                'ID'         => $post_id,
                'post_title' => $new_title
              );
            
            if( wp_update_post( $post_update )) {
                $this->JSONResponse("The title is changed!");     
            }
            else {
                $this->JSONResponse("The title was not changed!");     
            }
            
        }
        $this->JSONResponse("You need to be logged in to change the title!");     
    }
}

change_page_title::listen();


function change_page_title_shortcode() {
    global $post;
    $post_id = $post->ID;
    $message='<script>
    jQuery(document).ready(function(){
      jQuery("#btn_change_title").click(function(){
       
       jQuery.post("'.get_site_url().'/wp-admin/admin-ajax.php",{
            action: "change_page_title",
            page_id: "'.$post_id.'"
        },function(data){
            console.log(data)
        }, "JSON");
       
      });
    });
    </script>';
    $message.= '<p><a id="btn_change_title">Click Here to Change Title of this page to a random string!</a></p>';
 

    echo $message;

} 
// register shortcode
add_shortcode('change_page_title', 'change_page_title_shortcode'); 
