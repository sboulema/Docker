<?php
	// *******************************************
	// *** Database configuration (important!) ***
	// *******************************************

	define('DB_TYPE', getenv('TTRSS_DB_TYPE')); // pgsql or mysql
	define('DB_HOST', getenv('TTRSS_DB_HOST'));
	define('DB_USER', getenv('TTRSS_DB_USER'));
	define('DB_NAME', getenv('TTRSS_DB_NAME'));
	define('DB_PASS', getenv('TTRSS_DB_PASS'));
	define('DB_PORT', getenv('TTRSS_DB_PORT')); // usually 5432 for PostgreSQL, 3306 for MySQL

	define('MYSQL_CHARSET', getenv('TTRSS_MYSQL_CHARSET'));
	// Connection charset for MySQL. If you have a legacy database and/or experience
	// garbage unicode characters with this option, try setting it to a blank string.

	// ***********************************
	// *** Basic settings (important!) ***
	// ***********************************

	define('SELF_URL_PATH', getenv('TTRSS_SELF_URL_PATH'));
	// This should be set to a fully qualified URL used to access
	// your tt-rss instance over the net, such as: https://example.org/tt-rss/
	// The value should be a constant string literal. Please don't use
	// PHP server variables here - you might introduce security
	// issues on your install and cause hard to debug problems.
	// If your tt-rss instance is behind a reverse proxy, use the external URL.

	define('SINGLE_USER_MODE', getenv('TTRSS_SINGLE_USER_MODE'));
	// Operate in single user mode, disables all functionality related to
	// multiple users and authentication. Enabling this assumes you have
	// your tt-rss directory protected by other means (e.g. http auth).

	define('SIMPLE_UPDATE_MODE', getenv('TTRSS_SIMPLE_UPDATE_MODE'));
	// Enables fallback update mode where tt-rss tries to update feeds in
	// background while tt-rss is open in your browser.
	// If you don't have a lot of feeds and don't want to or can't run
	// background processes while not running tt-rss, this method is generally
	// viable to keep your feeds up to date.
	// Still, there are more robust (and recommended) updating methods
	// available, you can read about them here: https://tt-rss.org/wiki/UpdatingFeeds

	// *****************************
	// *** Files and directories ***
	// *****************************

	define('PHP_EXECUTABLE', getenv('TTRSS_PHP_EXECUTABLE'));
	// Path to PHP *COMMAND LINE* executable, used for various command-line tt-rss
	// programs and update daemon. Do not try to use CGI binary here, it won't work.
	// If you see HTTP headers being displayed while running tt-rss scripts,
	// then most probably you are using the CGI binary. If you are unsure what to
	// put in here, ask your hosting provider.

	define('LOCK_DIRECTORY', getenv('TTRSS_LOCK_DIRECTORY'));
	// Directory for lockfiles, must be writable to the user you run
	// daemon process or cronjobs under.

	define('CACHE_DIR', getenv('TTRSS_CACHE_DIR'));
	// Local cache directory for RSS feed content.

	define('ICONS_DIR', getenv('TTRSS_ICONS_DIR'));
	define('ICONS_URL', getenv('TTRSS_ICONS_URL'));
	// Local and URL path to the directory, where feed favicons are stored.
	// Unless you really know what you're doing, please keep those relative
	// to tt-rss main directory.

	// **********************
	// *** Authentication ***
	// **********************

	// Please see PLUGINS below to configure various authentication modules.

	define('AUTH_AUTO_CREATE', getenv('TTRSS_AUTH_AUTO_CREATE'));
	// Allow authentication modules to auto-create users in tt-rss internal
	// database when authenticated successfully.

	define('AUTH_AUTO_LOGIN', getenv('TTRSS_AUTH_AUTO_LOGIN'));
	// Automatically login user on remote or other kind of externally supplied
	// authentication, otherwise redirect to login form as normal.
	// If set to true, users won't be able to set application language
	// and settings profile.

	// *********************
	// *** Feed settings ***
	// *********************

	define('FORCE_ARTICLE_PURGE', getenv('TTRSS_FORCE_ARTICLE_PURGE'));
	// When this option is not 0, users ability to control feed purging
	// intervals is disabled and all articles (which are not starred)
	// older than this amount of days are purged.

	// ***********************************
	// *** Self-registrations by users ***
	// ***********************************

	define('ENABLE_REGISTRATION', getenv('TTRSS_ENABLE_REGISTRATION'));
	// Allow users to register themselves. Please be aware that allowing
	// random people to access your tt-rss installation is a security risk
	// and potentially might lead to data loss or server exploit. Disabled
	// by default.

	define('REG_NOTIFY_ADDRESS', getenv('TTRSS_REG_NOTIFY_ADDRESS'));
	// Email address to send new user notifications to.

	define('REG_MAX_USERS', getenv('TTRSS_REG_MAX_USERS'));
	// Maximum amount of users which will be allowed to register on this
	// system. 0 - no limit.

	// **********************************
	// *** Cookies and login sessions ***
	// **********************************

	define('SESSION_COOKIE_LIFETIME', getenv('TTRSS_SESSION_COOKIE_LIFETIME'));
	// Default lifetime of a session (e.g. login) cookie. In seconds,
	// 0 means cookie will be deleted when browser closes.

	// *********************************
	// *** Email and digest settings ***
	// *********************************

	// Tiny Tiny RSS sends mail via PHP mail() function, unless handled
	// by a plugin.

	// If you need SMTP support, take a look here:
	// https://git.tt-rss.org/fox/ttrss-mailer-smtp

	define('SMTP_FROM_NAME', getenv('TTRSS_SMTP_FROM_NAME'));
	define('SMTP_FROM_ADDRESS', getenv('TTRSS_SMTP_FROM_ADDRESS'));
	// Name, address and subject for sending outgoing mail. This applies
	// to password reset notifications, digest emails and any other mail.

	define('DIGEST_SUBJECT', getenv('TTRSS_DIGEST_SUBJECT'));
	// Subject line for email digests

	// ***************************************
	// *** Other settings (less important) ***
	// ***************************************

	define('CHECK_FOR_UPDATES', getenv('TTRSS_CHECK_FOR_UPDATES'));
	// Check for updates automatically if running Git version

	define('ENABLE_GZIP_OUTPUT', getenv('TTRSS_ENABLE_GZIP_OUTPUT'));
	// Selectively gzip output to improve wire performance. This requires
	// PHP Zlib extension on the server.
	// Enabling this can break tt-rss in several httpd/php configurations,
	// if you experience weird errors and tt-rss failing to start, blank pages
	// after login, or content encoding errors, disable it.

	define('PLUGINS', getenv('TTRSS_PLUGINS'));
	// Comma-separated list of plugins to load automatically for all users.
	// System plugins have to be specified here. Please enable at least one
	// authentication plugin here (auth_*).
	// Users may enable other user plugins from Preferences/Plugins but may not
	// disable plugins specified in this list.
	// Disabling auth_internal in this list would automatically disable
	// reset password link on the login form.

	define('LOG_DESTINATION', getenv('TTRSS_LOG_DESTINATION'));
	// Error log destination to use. Possible values: sql (uses internal logging
	// you can read in Preferences -> System), syslog - logs to system log.
	// Setting this to blank uses PHP logging (usually to http server
	// error.log).
	// Note that feed updating daemons don't use this logging facility
	// for normal output.

	define('CONFIG_VERSION', getenv('TTRSS_CONFIG_VERSION'));
	// Expected config version. Please update this option in config.php
	// if necessary (after migrating all new options from this file).

	// vim:ft=php
	$snippets = glob(getenv("SCRIPT_ROOT")."/config.d/*.php");

	foreach ($snippets as $snippet) {
		require_once $snippet;
	}


	define('NGINX_XACCEL_PREFIX', '/tt-rss');