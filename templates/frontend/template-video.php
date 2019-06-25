<?php
/**
 * Template Name: Template Videos 1
 *
 */

$videoCategory = get_category_by_slug( "video" );
$childrenVideoCategories = get_categories(array("parent" => $videoCategory->term_id));
$plugins_url = "/wp-content/plugins/Web-Manager/assets/";
$the_query = "cat=1111&posts_per_page=4&order=DESC&orderby=date&post_status=publish";
// echo $plugins_url;
// die();
//echo "<pre>";
//print_r($childrenVideoCategories);
//$x = get_post_meta( 5064, 'mfn-post-video', true );
//echo "</pre>";
//die();

get_header(); ?>

<?php echo do_shortcode('[blogVideoList the_query="'.$the_query.'" has_library=true]') ?>

<?php get_footer(); ?>