Feature: Test that WP Installer loads.

  Scenario: WP Installer loads correctly
    Given a WP install
    When I run `wp help installer`
    Then STDOUT should contain:
      """
      wp installer <command>
      """
      