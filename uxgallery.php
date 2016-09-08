<?php
/*
Plugin Name: WP Accessible gallery carousel plugin
Plugin URI:
Description: Accessible carousel extension for wordpress gallery
Author: Milenko Subotic
Version: 0.5
Author URI: https://rs.linkedin.com/in/suboticmilenko
*/

define('ACCESSIBLE_GALLERY_VERSION', '0.5');
define('ACCESSIBLE_GALLERY_URL', plugin_dir_url( __FILE__ ));
define('ACCESSIBLE_GALLERY_PATH', plugin_dir_path( __FILE__ ));
define('ACCESSIBLE_GALLERY_PLUGIN', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('ACCESSIBLE_GALLERY_PLUGIN_VERSION', '0.5');

class Acessible_Gallery_Plugin {

  function __construct() {

    $this->init();

  }

  function uxgal_scripts()
  {
      wp_register_script( 'slick-carousel', ACCESSIBLE_GALLERY_URL.'/assets/slick/slick.min.js', array('jquery') );
      wp_enqueue_script( 'slick-carousel' );
      wp_register_script( 'uxgal-script',  ACCESSIBLE_GALLERY_URL.'/assets/script.js', array('jquery'), null, 'false');
      wp_enqueue_script( 'uxgal-script' );
  }

  //register plugin styles

  function uxgal_styles()
  {
      wp_register_style( 'slick-style', plugins_url( '/assets/slick/slick.css', __FILE__ ), array(), null, 'all' );
      wp_enqueue_style( 'slick-style' );
      wp_register_style( 'uxgal-style', plugins_url( '/assets/uxgallery-style.css', __FILE__ ), array(), null, 'all' );
      wp_enqueue_style( 'uxgal-style' );
  }

  public function init() {
    add_action( 'wp_enqueue_scripts', array($this, 'uxgal_scripts') );
    add_action( 'wp_enqueue_scripts', array( $this, 'uxgal_styles') );
    add_filter( 'post_gallery', array( $this, 'uxgal_post_gallery'), 10, 2);
  }

  function uxgal_post_gallery($output, $attr) {
  global $post;

  if (isset($attr['orderby'])) {
      $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
      if (!$attr['orderby'])
          unset($attr['orderby']);
  }

  extract(shortcode_atts(array(
      'order' => 'ASC',
      'orderby' => 'menu_order ID',
      'id' => $post->ID,
      'itemtag' => 'dl',
      'icontag' => 'dt',
      'captiontag' => 'dd',
      'columns' => 3,
      'size' => 'thumbnail',
      'include' => '',
      'exclude' => ''
  ), $attr));

  $id = intval($id);
  if ('RAND' == $order) $orderby = 'none';

  if (!empty($include)) {
      $include = preg_replace('/[^0-9,]+/', '', $include);
      $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

      $attachments = array();
      foreach ($_attachments as $key => $val) {
          $attachments[$val->ID] = $_attachments[$key];
      }
      $total = count($attachments);
  }

  if (empty($attachments)) return '';

  $output = "<div id=\"slideshow_skip\"><a href=\"#slideshow_end\" tabindex='0'>Skip the image gallery</a></div>";
  $output .= "<div class=\"uxgal-slideshow-outter\">\n";
  $output .= "<div class=\"uxgal-slideshow-wrapper\">\n";

  $counter=1;

  foreach ($attachments as $id => $attachment) {

      $img = wp_prepare_attachment_for_js($id);
      $url = $img['url'];
      $height = $img['sizes']['full']['height'];
      $width = $img['sizes']['full']['width'];
      $alt = $img['alt'];
      $title = $img['title'];
      $caption = $img['caption'];
      $output .= "<figure>\n";
      $output .= "<img src=\"{$url}\" alt=\"{$alt}\" title=\"{$title}\" />\n";
      if ($caption) {
          $output .= "<figcaption>{$caption}</figcaption>\n";
      }
      $output .= '<div class="nav-controls clickable"><div class="nav-buttons">';
      if($counter>1){
          $output .= '<a role="button" href="#" class="nav-prev">back</a>';
      }
      if($counter<$total){
          $output .= '<a role="button" class="nav-next" href="#" >next</a>';
      }
      $output .= '</div></div>';
      $output .= "<div class=\"slideshow-counter\">\n";
      $output .= "<span class=\"slideshow-current\">1</span> of <span class=\"slideshow-total\"></span>";
      $output .= "</div>\n";
      $output .= "</figure>\n";
      $counter++;
  }

  $output .= "</div>\n";
  $output .= "</div>\n";
  $output .= "<div id=\"slideshow_end\"></div>";
  return $output;
  }

}

$gallery = new Acessible_Gallery_Plugin();


?>
