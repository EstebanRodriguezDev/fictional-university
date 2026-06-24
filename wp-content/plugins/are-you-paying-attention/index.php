<?php

/*
Plugin Name: Are You Paying Attention Quiz
Description: Give your readers a multiple choice question.
Version: 1.0
Author: Brad
Author URI: https://google.com
*/

if (! defined('ABSPATH')) exit;

class AreYourPayingAttention
{
  function __construct()
  {
    add_action('init', array($this, 'adminAssets'));
  }
  function adminAssets()
  {
    wp_register_style('quizeditcss', plugin_dir_url(__FILE__) . 'build/index.css');
    wp_register_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element', 'wp-block-editor'), filemtime(plugin_dir_path(__FILE__) . 'build/index.js'));
    register_block_type('ourplugin/are-you-paying-attention', array(
      'editor_script' => 'ournewblocktype',
      'editor_style' => 'quizeditcss',
      'render_callback' => array($this, 'theHTML'),
      'attributes' => array(
        'skyColor' => array('type' => 'string'),
        'grassColor' => array('type' => 'string')
      )
    ));
  }
  function theHTML($attributes)
  {
    ob_start(); ?>
    <p>Today the sky is <?php echo esc_html($attributes['skyColor']) ?> and the grass is <?php echo esc_html($attributes['grassColor']) ?>!!!</p>
<?php return ob_get_clean();
  }
}
$areYourPayingAttention = new AreYourPayingAttention();
