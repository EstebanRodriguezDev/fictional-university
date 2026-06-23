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
    add_action('enqueue_block_editor_assets', array($this, 'adminAssets'));
  }
  function adminAssets()
  {
    wp_enqueue_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element', 'wp-block-editor'), filemtime(plugin_dir_path(__FILE__) . 'build/index.js'));
  }
}

$areYourPayingAttention = new AreYourPayingAttention();
