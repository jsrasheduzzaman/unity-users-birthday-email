=== Users Birthday Email ===
Contributors: webfydev, jsrasheduzzaman
Donate link: //webfydev.com
Tags: birthday, user birthday email, birthday email, send birthday email, birth day email
Requires at least: 5.5.1
Tested up to: 7.0.1
Stable tag: 1.0.7.3
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

**Users Birthday Email** automatically sends personalized birthday emails to your WordPress users on their birthday.

The plugin is designed to be simple, lightweight, and easy to configure, making it suitable for both technical and non-technical users.

### Features

* Automatically sends birthday emails to WordPress users.
* Built-in date picker for collecting users' birthdays.
* Works independently without requiring a membership plugin.
* Easily integrates with third-party membership plugins using filter hooks.
* Multiple filter hooks for developers to customize functionality.
* User-friendly settings page with straightforward configuration.

For a better understanding of the plugin setup and features, please review the screenshots before configuring the plugin.

== Installation ==

### Modern Installation

1. Go to **WordPress Dashboard → Plugins → Add New Plugin**.
2. Search for **Users Birthday Email**.
3. Click **Install Now**.
4. Activate the plugin.

### Manual Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Go to **Plugins** in your WordPress dashboard.
3. Activate **Users Birthday Email**.

== Frequently Asked Questions ==

= Does this work without a membership plugin? =

Yes. The plugin does not require any membership plugin. It includes a built-in date picker that allows users to save their birthday directly.

= Can this plugin work with any membership plugin? =

Yes. The plugin provides several filter hooks that allow developers to integrate birthday data from virtually any membership plugin.

= Birthday emails are not being sent on time or are not being sent at all. What should I do? =

Before troubleshooting, please review the plugin screenshots and ensure the plugin has been configured correctly.

If the plugin is configured correctly but birthday emails are still not being sent, follow these steps:

* Verify that WP-Cron is running correctly.
* Ensure your website can send emails successfully (using an SMTP plugin is recommended).
* Check your server and PHP error logs for any errors.
* Confirm that scheduled WordPress cron events are executing as expected.
* Temporarily disable other plugins to check for conflicts.

If the issue persists, install and configure the plugin on a fresh WordPress installation with the default theme and no additional plugins.

* If the plugin works correctly on the fresh installation, the issue is likely caused by your current website environment, another plugin, your theme, or your server configuration. In this case, we recommend creating a staging environment and debugging your website to identify the conflict.
* If the plugin still does not work on the fresh WordPress installation after it has been configured correctly, please open a support ticket and include details about your WordPress version, PHP version, and the steps you followed. This will help us investigate the issue more efficiently.

== Screenshots ==

1. Configure the plugin from the Settings page.
2. Enter or update a user's birthday from the WordPress user profile.
3. Use the `[birthdate_form]` shortcode to display the birthday input field anywhere on your website.

== Developer Hooks ==

The plugin provides several filter hooks that allow developers to customize functionality and integrate with third-party membership plugins.

= unity_users_birthday_meta_key =

Change the user meta key used to store and retrieve user birthday data.

= unity_users_birth_date_query_args =

Modify the user query arguments before searching for users with matching birthdays.

= unity_users_birth_day_format =

Customize the birthday day format before comparing it with the current date.

= unity_users_birth_month_format =

Customize the birthday month format before comparing it with the current date.

= unity_users_birth_day_email_head_style =

Add or modify custom CSS styles inside the birthday email header.

== Changelog ==

= 1.0.7.3 - 2026-07-15 =

* Fixed: Readme file visibility issue

= 1.0.7.2 - 2026-07-15 =

* Fixed: Improved WP-Cron scheduling reliability for daily birthday email execution.
* Fixed: Added additional safety checks for email sending time comparison.

= 1.0.7.1 - 2025-04-23 =

* Added: `unity_users_birth_day_email_head_style` filter hook for customizing email header styles.

= 1.0.7 - 2025-01-02 =

* Fixed: Birthday emails not sending in certain cases.

= 1.0.6 - 2024-05-22 =

* Fixed: User birthday meta update issue.

= 1.0.5 - 2024-04-22 =

* Added: Documentation URL.

= 1.0.4 - 2024-04-22 =

* Added: Birthday form shortcode.
* Improved: Hooks and extensibility.

= 1.0.3 - 2024-02-18 =

* Fixed: Missing placeholder image.

= 1.0.2 - 2024-02-18 =

* Added: Plugin images and placeholder image.

= 1.0.1 - 2024-02-11 =

* Fixed: Missing placeholder image.

= 1.0.0 - 2024-02-09 =

* Initial release.

== Upgrade Notice ==

= 1.0.7.2 =

Update to improve WP-Cron reliability and ensure birthday emails are processed more consistently.
