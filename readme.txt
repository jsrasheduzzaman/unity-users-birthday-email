=== Users Birthday Email ===
Contributors: webfydev, jsrasheduzzaman
Donate link: //webfydev.com
Tags: birthday, user birthday email, birthday email, send birthday email, birth day email
Requires at least: 5.5.1
Tested up to: 7.1
Stable tag: 1.0.8.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Users Birthday Email sends personalized birthday messages to WordPress users. Administrators can review users whose birthdays are today or tomorrow, send an email immediately to selected users celebrating today, and configure the automatic email schedule and message template.

The plugin includes a birthday date field on user profiles and can also integrate with membership plugins or custom user data through developer filters.

== Features ==

* Automatically send birthday emails using a customizable HTML message and subject.
* Enable or disable birthday email sending from the settings page.
* Review today's birthday users and send to one or multiple selected users immediately.
* Review tomorrow's birthday users in a separate list.
* Preview the next scheduled WordPress cron run in UTC.
* Configure the UTC hour when automatic birthday emails are sent.
* Optionally send an administrator notification when a birthday email is delivered.
* Add or update birthdays on WordPress user profiles, or display the birthday form using the `[birthdate_form]` shortcode.
* Integrate with other plugins using the available developer filters.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`, or install the plugin from **Plugins > Add New Plugin**.
2. Activate **Users Birthday Email** from the WordPress Plugins screen.
3. Open **Users > Birthday Emails Settings** to configure automatic sending and the email template.
4. Add birthdays to user profiles, or integrate an existing birthday field using the developer filters.

== Settings and Usage ==

Open **Users > Birthday Emails Settings** to manage birthday email delivery.

* **Next Scheduled Run (UTC):** Displays the next run of the plugin's hourly WordPress cron event when it is scheduled. This is the next cron check, not necessarily the email delivery time.
* **Enable Birthday Email Notification:** Controls both automatic and administrator-triggered birthday emails. Existing installations remain enabled unless this option is turned off.
* **When to Start Sending Emails:** Select the UTC hour during which the hourly cron event sends birthday emails. WP-Cron depends on site traffic unless a server cron job is configured to run `wp-cron.php`.
* **From Name and From Email:** Set the sender shown on birthday emails.
* **Send Notification Too:** Optionally send a separate notification to the configured notification email address after a birthday message is sent successfully.
* **Email Subject and Email Description:** Configure the subject and HTML message. The template supports `@username@`, `@fullname@`, `@firstname@`, `@lastname@`, `@nickname@`, and `@displayname@` placeholders.

The **Birthdays Today** table lists matching users and provides individual checkboxes plus a select-all checkbox. Choose one or more users and click **Send Instant Birthday Emails** to send immediately. The **Birthdays Tomorrow** table is a preview list and does not send emails.

== Frequently Asked Questions ==

= Does the plugin require a membership plugin? =

No. The plugin adds a birthday field to WordPress user profiles and can be used independently.

= Can I use birthday data stored by another plugin? =

Yes. Use the `unity_users_birthday_meta_key` and `unity_users_birth_date_query_args` filters to connect a different user meta key or customize the query.

= What time zone is used for automatic sending? =

The configured sending hour and birthday date matching use UTC. The next scheduled run preview also displays UTC. WordPress cron runs hourly and checks whether the configured UTC sending hour has arrived.

= What if birthday emails are not sent? =

Before troubleshooting, please review the plugin screenshots and ensure the plugin has been configured correctly.

* Confirm that birthday email sending is enabled, the user's birth date is saved, and the configured UTC sending hour has passed.
* Then check that WordPress cron is running and that the site can send mail.
* An SMTP plugin can help diagnose mail delivery. If `DISABLE_WP_CRON` is enabled, configure a real server cron job to request `wp-cron.php` regularly; dismissing the admin notice does not enable cron.

If the issue persists, temporarily disable other plugins to check for conflicts. For further check, install and configure the plugin on a fresh WordPress installation with the default theme and no additional plugins.

* If the plugin works correctly on the fresh installation, the issue is likely caused by your current website environment, another plugin, your theme, or your server configuration. In this case, we recommend creating a staging environment and debugging your website to identify the conflict.
* If the plugin still does not work on the fresh WordPress installation after it has been configured correctly, please open a support ticket and include details about your WordPress version, PHP version, and the steps you followed. This will help us investigate the issue more efficiently.

= Can I send birthday emails manually? =

Yes. On the settings page, select one or more users listed under **Birthdays Today** and click **Send Instant Birthday Emails**. Manually sending is also blocked when birthday email sending is disabled.

== Screenshots ==

1. Birthday email settings, schedule preview.
2. Macros, Today's and tomorrow's birthday lists with bulk emails sender.
3. Add or update a user's birthday from their WordPress profile.
4. Use the `[birthdate_form]` shortcode to display a birthday field on the site.

== Developer Hooks ==

= `unity_users_birthday_meta_key` =

Filter the user meta key used to store and retrieve birthday dates. Defaults to `unity-birth-date`.

= `unity_users_birth_date_query_args` =

Filter the `WP_User_Query` arguments used to find users with a birthday matching the target day and month.

= `unity_users_birth_day_format` =

Filter the birthday day value before it is compared with the target day.

= `unity_users_birth_month_format` =

Filter the birthday month value before it is compared with the target month.

= `unity_users_birth_day_email_head_style` =

Filter CSS added inside the birthday email's `<head>` element.

== Changelog ==

= 1.0.8.0 - 2026-09-28 =

* Added an administrator control to enable or disable birthday email sending.
* Added today's birthday list with individual and bulk manual sending.
* Added a list of users whose birthdays are tomorrow.
* Added a preview of the next scheduled cron run in UTC.
* Improved cron warning visibility and dismissal behavior.

= 1.0.7.3 - 2026-07-15 =

* Fixed readme file visibility issue.

= 1.0.7.2 - 2026-07-15 =

* Improved WP-Cron scheduling reliability for daily birthday email execution.
* Added safety checks for email sending time comparison.

= 1.0.7.1 - 2025-04-23 =

* Added the `unity_users_birth_day_email_head_style` filter hook.

= 1.0.7 - 2025-01-02 =

* Fixed birthday emails not sending in certain cases.

= 1.0.6 - 2024-05-22 =

* Fixed user birth date meta update issue.

= 1.0.5 - 2024-04-22 =

* Added documentation URL.

= 1.0.4 - 2024-04-22 =

* Added birthday form shortcode.
* Improved hooks and extensibility.

= 1.0.3 - 2024-02-18 =

* Fixed missing placeholder image.

= 1.0.2 - 2024-02-18 =

* Added plugin images and placeholder image.

= 1.0.1 - 2024-02-11 =

* Fixed missing placeholder image.

= 1.0.0 - 2024-02-09 =

* Initial release.

== Upgrade Notice ==

= 1.0.8.0 =

Review the new birthday lists and email controls under **Users > Birthday Emails Settings**. Automatic sending requires WordPress cron to run, and the configured delivery hour is in UTC.
