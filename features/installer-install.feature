Feature: Test the `wp installer install` command.

  Scenario: Install WordPress
    When I run `wp installer install wptest --url=http://localhost/wptest --title=Test --admin_user=test --admin_email=test@example.com --site_base_path=/tmp/ --dbuser=root --dbpass=root --dbhost=127.0.0.1`
    Then STDOUT should contain:
      """
      Downloading WordPress...
      Creating wp-config.php...
      Creating the database...
      """
    And the /tmp/wptest directory should contain:
      """
      index.php
      license.txt
      readme.html
      wp-activate.php
      wp-admin
      wp-blog-header.php
      wp-comments-post.php
      wp-config-sample.php
      wp-config.php
      wp-content
      wp-cron.php
      wp-includes
      wp-links-opml.php
      wp-load.php
      wp-login.php
      wp-mail.php
      wp-settings.php
      wp-signup.php
      wp-trackback.php
      xmlrpc.php
      """
    And the database for the install at /tmp/wptest should exist