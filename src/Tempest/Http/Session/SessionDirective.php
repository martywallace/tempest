<?php namespace Tempest\Http\Session;

use Tempest\Enums\Enum;

/**
 * Session initialization directives.
 *
 * @author Ascension Web Development
 */
class SessionDirective extends Enum {

	const SAVE_PATH = 'save_path';
	const NAME = 'name';
	const SAVE_HANDLER = 'save_handler';
	const AUTO_START = 'auto_start';
	const GC_PROBABILITY = 'gc_probability';
	const GC_DIVISOR = 'gc_divisor';
	const GC_MAXLIFETIME = 'gc_maxlifetime';
	const SERIALIZE_HANDLER = 'serialize_handler';
	const COOKIE_LIFETIME = 'cookie_lifetime';
	const COOKIE_PATH = 'cookie_path';
	const COOKIE_DOMAIN = 'cookie_domain';
	const COOKIE_SECURE = 'cookie_secure';
	const COOKIE_HTTPONLY = 'cookie_httponly';
	const USE_STRICT_MODE = 'use_strict_mode';
	const USE_COOKIES = 'use_cookies';
	const USE_ONLY_COOKIES = 'use_only_cookies';
	const REFERER_CHECK = 'referer_check';
	const CACHE_LIMITER = 'cache_limiter';
	const CACHE_EXPIRE = 'cache_expire';
	const USE_TRANS_SID = 'use_trans_sid';
	const TRANS_SID_TAGS = 'trans_sid_tags';
	const TRANS_SID_HOSTS = 'trans_sid_hosts';
	const SID_LENGTH = 'sid_length';
	const SID_BITS_PER_CHARACTER = 'sid_bits_per_character';
	const UPLOAD_PROGRESS_ENABLED = 'upload_progress.enabled';
	const UPLOAD_PROGRESS_CLEANUP = 'upload_progress.cleanup';
	const UPLOAD_PROGRESS_PREFIX = 'upload_progress.prefix';
	const UPLOAD_PROGRESS_NAME = 'upload_progress.name';
	const UPLOAD_PROGRESS_FREQ = 'upload_progress.freq';
	const UPLOAD_PROGRESS_MIN_FREQ = 'upload_progress.min_freq';
	const LAZY_WRITE = 'lazy_write';

}