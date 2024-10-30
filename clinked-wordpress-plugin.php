<?php
/**
 * Plugin Name: Clinked Client Portal
 * Plugin URI:  https://clinked.com
 * Description: The Clinked Client Portal plugin is a great addition to the popular <a href="https://clinked.com">Clinked application</a> - a branded, feature rich client portal. Using the plugin couldn't be easier. Simply include the shortcode <strong>[clinked-login-button]</strong> in the desired page and you're done. If you don't already have an account visit our website and <a href="https://clinked.com/packages">sign up</a> for a free trial.
 * Version:     1.9
 * Author:      Rabbitsoft Ltd.
 * Author URI:  https://rabbitsoft.com
 * License:     MIT
 *
 * @link https://clinked.com
 *
 * @package Clinked
 * @version 1.9
 */

/*
Copyright (c) 2023 Rabbitsoft Ltd

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

define( 'CLINKED_WP_VERSION', '1.1' );
define( 'CLINKED_WP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

function _action($name, $handler) {
  add_action("wp_ajax_" . $name, $handler);
  add_action("wp_ajax_nopriv_" . $name, $handler);
}

class ClinkedLoginButton_Widget extends BaseClinked_Widget {
  protected $widget_slug = "clinked-login-button";
  protected $widget_name = "Clinked Login Button Widget";

  protected static $shortcode = 'clinked-login-button';


  public static function get_widget($atts = [], $content = null, $tag = '') {
    $atts = shortcode_atts([
      "portal_url" => "https://app.clinked.app",
      "button_class" => "",
      "button_text" => "Log in",
      "text" => "",
      "widget_id" => ""
      ], $atts);

    if (empty($atts['widget_id'])) {
      global $_SESSION;
      $widgetOptions = (object) $atts;

      // store a random widget options in the sesssion
      $widget_id = "clinked-login-" . spl_object_hash($widgetOptions);
      $_SESSION[$widget_id] = $widgetOptions;

      $atts['widget_id'] = $widget_id;
    }

    $containerClasses = Array('clinked-login-container');
    $buttonClasses = Array('wp-block-button', 'has-custom-font-size', 'has-small-font-size');
    if (!empty($atts['button_class'])) {
      $buttonClasses = array_merge($buttonClasses, explode(',', $atts['button_class']));
    }
    $atts['buttonClasses'] = $buttonClasses;

    $widget = '<div class="clinked-login-container"  data-widget="' . $atts["widget_id"] . '">';
    if (!empty($atts["text"])) {
      $widget .= wpautop(wp_kses_post($atts["text"]));
    }
    $widget .= self::build_view($atts["widget_id"], 'button', $atts);
    $widget .= '</div>';

    return $widget;
  }

  public static function build_view($widget, $view, $model = []) {
    if (empty($widget) || empty($view) || !preg_match("/^[a-zA-Z_]+$/", $view)) {
      http_response_code(400);
    }
    // Attempt to call function that would build the model for view
    $fn = [__CLASS__, "handle_" . $view . "_model"];
    if (is_callable($fn)) {
      $model = array_merge($model, call_user_func($fn, $model));
    }
    return self::render_view($view, $model);
  }

  public function __construct() {
    $this->widget_name = esc_html__("Clinked Login Button", "clinked");
    $this->default_widget_title = esc_html__("Clinked Contact Area", "clinked");

    parent::__construct($this->widget_slug, $this->widget_name, [
        "classname" => $this->widget_slug,
        "description" => esc_html__("Please use your credentials to login", "clinked"),
      ]);

    add_shortcode(self::$shortcode, [__CLASS__, "get_widget"]);
  }
}


class ClinkedLogin_Widget extends BaseClinked_Widget {
  protected $widget_slug = "clinked-login-widget";
  protected $widget_name = "Clinked Login Widget";

  protected static $shortcode = 'clinked-login';

  public static function get_widget($atts = [], $content = null, $tag = '') {
    $atts = shortcode_atts([
      "portal_url" => "https://app.clinked.com",
      "password_placeholder" => "Password",
      "email_placeholder" => "E-mail address",
      "username_label" => "",
      "password_label" => "",
      "container_class" => "",
      "input_class" => "",
      "button_class" => "",
      "before_widget" => "",
      "after_widget" => "",
      "before_title" => "",
      "after_title" => "",
      "title" => "",
      "text" => "",
      "widget_id" => "",
      "forgotten_password_text" => "Forgotten Password?",
      "forgotten_password" => false,
      "remember_me" => false,
      "remember_me_text" => "Remember me"
      ], $atts);

    if (empty($atts['widget_id'])) {
      global $_SESSION;

      $widgetOptions = (object) $atts;

      // store a random widget options in the sesssion
      $widget_id = "clinked-" . spl_object_hash($widgetOptions);

      $_SESSION[$widget_id] = $widgetOptions;
      $atts['widget_id'] = $widget_id;
    }

    $containerClasses = Array('clinked-container');
    if (!empty($atts['container_class'])) {
      array_push($containerClasses, $atts['container_class']);
    }
    $inputClasses = Array();
    if (!empty($atts['input_class'])) {
      $inputClasses = array_merge($inputClasses, explode(',', $atts['input_class']));
    }
    $atts['inputClasses'] = $inputClasses;

    $buttonClasses = Array();
    if (!empty($atts['button_class'])) {
      $buttonClasses = array_merge($buttonClasses, explode(',', $atts['button_class']));
    }
    $atts['buttonClasses'] = $buttonClasses;

    $widget = '<div class="' . join(' ', $containerClasses) . '" data-ajax="' . admin_url("admin-ajax.php") . '" data-widget="' . $atts["widget_id"] . '">';
    // Before widget hook.
    $widget .= $atts["before_widget"];
    $widget .= ($atts["title"]) ? $atts["before_title"] . '<h2>' . esc_html($atts["title"]) . '</h2>'. $atts["after_title"] : "";
    $widget .= wpautop(wp_kses_post($atts["text"]));

    $widget .= self::build_view($atts["widget_id"], 'login', $atts);
    $widget .= '</div>';

    // After widget hook.
    $widget .= $atts["after_widget"];
    return $widget;
  }

  /**
   * Render a view.
   *
   * @param unknown $widget widget
   * @param unknown $view view
   */
  public static function build_view($widget, $view, $model = []) {
    if (empty($widget) || empty($view) || !preg_match("/^[a-zA-Z_]+$/", $view)) {
      http_response_code(400);
    }
    // Attempt to call function that would build the model for view
    $fn = [__CLASS__, "handle_" . $view . "_model"];
    if (is_callable($fn)) {
      $model = array_merge($model, call_user_func($fn, $model));
    }
    return self::render_view($view, $model);
  }

  public function __construct() {
    $this->widget_name = esc_html__("Clinked Contact Area", "clinked");
    $this->default_widget_title = esc_html__("Clinked Contact Area", "clinked");

    parent::__construct($this->widget_slug, $this->widget_name, [
        "classname" => $this->widget_slug,
        "description" => esc_html__("Please use your credentials to login", "clinked"),
      ]);

    add_shortcode(self::$shortcode, [__CLASS__, "get_widget"]);
    // ajax actions
    _action("clinked_login", [$this, "handle_login_action"]);
  }

  public function handle_login_action() {
    $username = urlencode($_POST["username"]);
    $password = urlencode($_POST["password"]);

    # Authenticate using username and password to check if credentials are valid
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api-p1.clinked.com/oauth/token?grant_type=password&client_id=clinked-mobile&username=$username&password=$password",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
    ));
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err || $httpcode < 200 || $httpcode > 299) {
      http_response_code(400);
      echo $response;
    } else {
      http_response_code(200);
      echo "{}";
    }
    die();
  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    echo self::get_widget();
  }
}

abstract class BaseClinked_Widget extends WP_Widget {
  public static function render_view($name, $model) {
    if (!preg_match("/^[\w_]+$/", $name)) {
      return "Unknown template";
    }
    $path = self::dir("views/" . $name . ".php");
    if (!file_exists($path)) {
      return "Unknown template";
    }
    if (is_string($model) && is_callable($model)) {
      $model = call_user_func_array($model);
    }
    ob_start();
    if (is_array($model)) {
      foreach ($model as $key => $value) {
        ${$key} = $value;
      }
    }
    include($path);
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
  }

  public static function dir($path = "") {
    static $dir;
    $dir = $dir ? $dir : trailingslashit(dirname(__FILE__));
    return $dir . $path;
  }

  public static function url($path = "") {
    static $url;
    $url = $url ? $url : trailingslashit(plugin_dir_url(__FILE__));
    return $url . $path;
  }

}

function clinked_register() {
  wp_enqueue_script("clinked-widget", ClinkedLogin_Widget::url("assets/js/clinked.js"), ["jquery"]);

  register_widget("ClinkedLogin_Widget");
  register_widget("ClinkedLoginButton_Widget");
  if (!session_id()) {
    session_start();
  }
}

add_action("widgets_init", "clinked_register");
