<div class="clinked-error" style="display: none">
    <?php esc_html_e('Invalid e-mail address or password.', 'clinked' ); ?>
</div>
<form method="post" class="clinked-login" action="<?php echo $portal_url."/login" ?>">
  <input type="hidden" name="login-url" value="<?php echo $portal_url."/login" ?>">
  <input type="hidden" name="action" value="clinked_login">

  <?php wp_nonce_field( 'clinked_login' ); ?>
  <p class="clinked-form-group">
    <?php if (!empty($username_label)): ?>
        <label for="clinked-login-form-username"><?php echo _e($username_label, 'clinked'); ?></label>
    <?php endif; ?>
    <input id="clinked-login-form-username" class="<?php echo join($inputClasses); ?>" type="text" name="username" autocomplete="off" placeholder="<?php _e($email_placeholder, 'clinked'); ?>" value="<?php echo @$_POST["username"] ?>">
  </p>
  <p class="clinked-form-group">
    <?php if (!empty($password_label)): ?>
        <label for="clinked-login-form-password"><?php echo _e($password_label, 'clinked'); ?></label>
    <?php endif; ?>
    <input id="clinked-login-form-password" class="<?php echo join(' ', $inputClasses); ?>" type="password" name="password" autocomplete="off" placeholder="<?php _e($password_placeholder, 'clinked'); ?>" value="<?php echo @$_POST["password"] ?>">
  </p>
  <p class="clinked-form-group">
    <button type="submit" class="<?php echo join(' ', $buttonClasses); ?>"  id="clinked-submit-button"><?php _e('Login', 'clinked'); ?></button>
    <?php if (!empty($remember_me)): ?>
      <label class="inline-block">
        <input type="checkbox" name="remember-me">
        <?php _e($remember_me_text, 'clinked'); ?>
      </label>
    <?php endif; ?>
  </p>

  <?php if (!empty($forgotten_password)): ?>
    <a href="<?php echo $portal_url ?>/password/forgotten"><?php _e($forgotten_password_text, 'clinked'); ?></a>
  <?php endif; ?>
</form>
