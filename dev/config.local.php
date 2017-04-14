<?php
class Config extends ConfigDefault
{
	static $DB_HOST = "<DB_HOST>";
	static $DB_USER = "<DB_USER>";
	static $DB_PASSWD = "<DB_PASSWD>";
	static $DB_NAME = "<DB_NAME>";

	static $DOMAIN = "TODO.dev.ridi.io";
	static $MISC_URL = "http://misc.ridibooks.com";
	static $ACTIVE_URL = "http://active.ridibooks.com";
	
	static $SHOP_URL = "shop.dev.ridi.io";

	static $ENABLE_SSL = false;

	static $MEMCACHE_ENABLE = false;
	static $SESSION_USE_MEMCACHE = false;
	static $COUCHBASE_ENABLE = false;

	static $CMS_RPC_URL = 'http://localhost:8000';

	// ridi.io
	static $FACEBOOK_ID = '<FACEBOOK_ID>';
	static $FACEBOOK_SECRET_ID = '<FACEBOOK_SECRET_ID>';

	static $UNDER_DEV = true;
	static $ORM_IS_DEV_MODE = true;

	static $API_SERVER_URL = "http://api.TODO.dev.ridi.io";
	static $API_SERVER_URL_PROXY = "http://TODO.dev.ridi.io/noti";
	static $API_SERVER_URL_ADMIN = "http://api.TODO.dev.ridi.io";
}

