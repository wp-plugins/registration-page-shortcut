<?php
/*
  Plugin Name: Registration Page Shortcut
  Plugin URI: https://wordpress.org/plugins/registration-page-shortcut
  Description: Show registration form on a page, just add [registration_form] to the page to show the form.
  Version: 1.0
  Author: Sonny Nguyen
  Author URI: https://github.com/phplaw
 */

add_shortcode('registration_form', 'registration_form_handle');


function registration_form_handle() {
  global $errors;
  $registered = false;
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
  $user_login = '';
  $user_email = '';
  if ( $http_post ) {
    $user_login = $_POST['user_login'];
    $user_email = $_POST['user_email'];
    $errors = register_new_user($user_login, $user_email);
    if ( !is_wp_error($errors) ) {
      /*$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'wp-login.php?checkemail=registered';
      wp_safe_redirect( $redirect_to );
      exit();*/
      $registered = true;
    }
  }
  ob_start();
  // if is not registered
  if ($registered === false) :
    /*action="<?php echo esc_url( site_url('wp-login.php?action=register', 'login_post') ); ?>"*/
    echo get_registration_page_error($errors);
    ?>
    <form name="registerform" id="registerform" method="post" novalidate="novalidate">
      <p>
        <label for="user_login"><?php _e('Username') ?><br />
          <input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(wp_unslash($user_login)); ?>" size="20" /></label>
      </p>
      <p>
        <label for="user_email"><?php _e('E-mail') ?><br />
          <input type="email" name="user_email" id="user_email" class="input" value="<?php echo esc_attr( wp_unslash( $user_email ) ); ?>" size="25" /></label>
      </p>
      <?php
      /**
       * Fires following the 'E-mail' field in the user registration form.
       *
       * @since 2.1.0
       */
      do_action( 'register_form' );
      ?>
      <p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
      <br class="clear" />
      <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Register'); ?>" /></p>
    </form>

    <p id="nav">
      <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log in' ); ?></a> |
      <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ) ?>"><?php _e( 'Lost your password?' ); ?></a>
    </p>
  <?php
  else:
    ?>
    <div class="registration_completed"><?php _e('Thanks for registration with us, we will check and approve your account soon.') ?></div>
  <?php
  endif;
  // END registered
  return ob_get_clean();
}

function get_registration_page_error($wp_error) {
  $output = '';
  if ( is_wp_error($wp_error) ) {
    $errors = '';
    $messages = '';
    foreach ( $wp_error->get_error_codes() as $code ) {
      $severity = $wp_error->get_error_data( $code );
      foreach ( $wp_error->get_error_messages( $code ) as $error_message ) {
        if ( 'message' == $severity )
          $messages .= '	' . $error_message . "<br />\n";
        else
          $errors .= '<li>' . $error_message . "</li>";
      }
    }
    if ( ! empty( $errors ) ) {
      /**
       * Filter the error messages displayed above the login form.
       *
       * @since 2.1.0
       *
       * @param string $errors Login error message.
       */
      //$output =  '<ul id="login_error">' . apply_filters( 'login_errors', $errors ) . "</ul>";
      $output .=  '<ul id="login_error" class="woocommerce-error">' . $errors . "</ul>";
    }
    if ( ! empty( $messages ) ) {
      /**
       * Filter instructional messages displayed above the login form.
       *
       * @since 2.5.0
       *
       * @param string $messages Login messages.
       */
      $output .=  '<p class="message">' . apply_filters( 'login_messages', $messages ) . "</p>\n";
    }
  }

  return '<div class="woocommerce">' . $output . '</div>';
}