<?php
/*
Author: Matters OF The Earth
URL: http://www.mattersoftheearth.com
*/
// -------------------------------------------------------------------------------------------------
// FoundationPress setup
// -------------------------------------------------------------------------------------------------

// Various clean up functions
require_once('library/cleanup.php');

// Required for Foundation to work properly
require_once('library/foundation.php');

// Register all navigation menus
require_once('library/navigation.php');

// Add menu walker
require_once('library/menu-walker.php');

// Create widget areas in sidebar and footer
require_once('library/widget-areas.php');

// Return entry meta information for posts
require_once('library/entry-meta.php');

// Enqueue scripts
require_once('library/enqueue-scripts.php');

// Add theme support
require_once('library/theme-support.php');

// -------------------------------------------------------------------------------------------------
// Other Setup
// -------------------------------------------------------------------------------------------------

// Enable Featured Image (For BadgeOS)
// -------------------------------------------------------------------------------------------------

add_theme_support( 'post-thumbnails' );

// Hide content for all Users
// -------------------------------------------------------------------------------------------------

remove_action("admin_color_scheme_picker", "admin_color_scheme_picker");

// Hide content for Subscrbers
// -------------------------------------------------------------------------------------------------

function bp_core_admin_bar__for_theme() {
  echo '';
}

if ( is_user_logged_in() && current_user_is_subscriber() ) {
  remove_action('wp_footer', 'bp_core_admin_bar', 6);
  add_action( 'wp_footer', 'bp_core_admin_bar__for_theme', 8 ); // Buddypress menu bar
}


// -------------------------------------------------------------------------------------------------
// UI Helpers
// -------------------------------------------------------------------------------------------------

// Profile details
// -------------------------------------------------------------------------------------------------

function profile_display_name() {
    $current_user = wp_get_current_user();
    return $current_user->display_name;
}

// -------------------------------------------------------------------------------------------------
// UI Rendering
// -------------------------------------------------------------------------------------------------

// Render sidebar content
// -------------------------------------------------------------------------------------------------

function render_course_sidebar_content() {
  if ( is_user_logged_in() ) {
    echo do_shortcode("[course_content course_id='8']");
    echo do_shortcode("[learndash_course_progress]");
  }
}

// Render the menus used in the top-bar
// -------------------------------------------------------------------------------------------------

function render_site_menu() {
  $html = '<ul class="left SiteMenu">';
  $html .= '<li><a class="course-link" href="' . home_url('/courses/gender-stereotypes/') . '">Course</a></li>';
  $html .= '<li><a class="glossary-link" href="' . home_url('/glossary') . '">Glossary</a></li>';
  $html .= '</ul>';
  return $html;
}

function render_user_menu() {
  $html = '<ul class="right MembersMenu">';
  if ( is_user_logged_in() ) {
    if ( current_user_is_admin() ){
      $html .= '<li ><a class="admin-link" href="' . admin_url() . '"> Admin </a></li>';
    }
    if ( is_user_logged_in() ) {
      $html .= '<li><a class="members-link" href="' . get_page_link(get_page_by_title( 'Members' )->ID) . '">Course Members</a></li>';
    }
    $html .= '<li ><a href="' . bp_loggedin_user_domain() . '">Your Profile: ' . profile_display_name() .'</a></li>';
    $html .= '<li class=""><a href="' . wp_logout_url('$index.php') . '">Logout</a></li>';
  } else {
    $html .= '<li class=""><a href="'. wp_login_url().'">Login</a></li>';
  }
  $html .= '</ul>';
  return $html;
}

// Render footer logo-links
// -------------------------------------------------------------------------------------------------

function render_logo_links() {
  $html =  '<a href="#">';
  $html .=  '<img src="' . get_bloginfo('template_directory') . '/assets/img/images_voices-of-change-logo.png" alt="Voices Of Change Logo">';
  $html .= '</a>';
  $html .= '<a href="#">';
  $html .=  '<img src="'  . get_bloginfo('template_directory') .  '/assets/img/images_uk-aid-logo.png" alt="UK Aid Logo">';
  $html .=  '</a>';
  return $html;
}

// -------------------------------------------------------------------------------------------------
// Authorization
// -------------------------------------------------------------------------------------------------

// Check User Role
// Checking whether a user can edit posts is enough.
// -------------------------------------------------------------------------------------------------

function current_user_is_subscriber() {
  return !current_user_can( 'edit_posts' );
}

function current_user_is_admin() {
  return current_user_can( 'edit_posts' );
}

function member_profile_belongs_to_current_user() {
  return bp_is_my_profile();
}

function current_user_can_view_learndash_profile() {
  return (member_profile_belongs_to_current_user() || current_user_is_admin());
}

// Redirect back to homepage and not allow access to WP admin for Subscribers.
// However, there might be admin access via AJAX even for non-admins, so make an exception if it's
// an Ajax request.
// -------------------------------------------------------------------------------------------------

function themeblvd_redirect_admin(){

  if ( !current_user_is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ){
    wp_redirect( site_url() );
    exit;
  }
}

add_action( 'admin_init', 'themeblvd_redirect_admin' );

// Login Redirection
// -------------------------------------------------------------------------------------------------

function buddypress_login_redirection_for_non_admins($redirect_to_calculated,$redirect_url_specified,$user)
{

  if ( !is_wp_error($user) ){

    if(empty($redirect_to_calculated))
    {
      $redirect_to_calculated=admin_url();
    }

    if(!user_can($user, 'edit_posts')){
      return bp_core_get_user_domain($user->ID );
    }
  }
  return $redirect_to_calculated; /*if site admin or not logged in,do not do anything much*/
}

add_filter("login_redirect","buddypress_login_redirection_for_non_admins",10,3);


// -------------------------------------------------------------------------------------------------
// User Setup
// -------------------------------------------------------------------------------------------------

// Register User To Course Automatically On Registration
// -------------------------------------------------------------------------------------------------

function register_user_to_course($user_id) {
  $course_id = 8;
  ld_update_course_access($user_id, $course_id, $remove = false);
}

add_action( 'user_register', 'register_user_to_course', 10, 1 );

//
//
//

function check_last_pdf_download($ll, $pid ) {
	  		error_log("Caught completion / mark etc ".$ll."  ".$pid."\n", 3, "../my-errors.log");
	  		error_log($link."  ".$post_id."\n", 3, "../my-errors.log");
}

add_filter('learndash_completion_redirect', check_last_pdf_download($link, $post_id ),5,2);

?>