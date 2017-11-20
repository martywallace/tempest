<?php namespace Tempest\Http\Session;

use Tempest\Data\Enum;

/**
 * Session initialization directives.
 *
 * @author Marty Wallace
 */
class Directive extends Enum {

	const SAVE_PATH = 'session.save_path';
	const NAME = 'session.name';
	const SAVE_HANDLER = 'session.save_handler';
	const AUTO_START = 'session.auto_start';
	const GC_PROBABILITY = 'session.gc_probability';
	const GC_DIVISOR = 'session.gc_divisor';
	const GC_MAXLIFETIME = 'session.gc_maxlifetime';
	const SERIALIZE_HANDLER = 'session.serialize_handler';
	const COOKIE_LIFETIME = 'session.cookie_lifetime';
	const COOKIE_PATH = 'session.cookie_path';
	const COOKIE_DOMAIN = 'session.cookie_domain';
	const COOKIE_SECURE = 'session.cookie_secure';
	const COOKIE_HTTPONLY = 'session.cookie_httponly';
	const USE_STRICT_MODE = 'session.use_strict_mode';
	const USE_COOKIES = 'session.use_cookies';
	const USE_ONLY_COOKIES = 'session.use_only_cookies';
	const REFERER_CHECK = 'session.referer_check';
	const CACHE_LIMITER = 'session.cache_limiter';
	const CACHE_EXPIRE = 'session.cache_expire';
	const USE_TRANS_SID = 'session.use_trans_sid';
	const TRANS_SID_TAGS = 'session.trans_sid_tags';
	const TRANS_SID_HOSTS = 'session.trans_sid_hosts';
	const SID_LENGTH = 'session.sid_length';
	const SID_BITS_PER_CHARACTER = 'session.sid_bits_per_character';
	const UPLOAD_PROGRESS_ENABLED = 'upload_progress.enabled';
	const UPLOAD_PROGRESS_CLEANUP = 'upload_progress.cleanup';
	const UPLOAD_PROGRESS_PREFIX = 'upload_progress.prefix';
	const UPLOAD_PROGRESS_NAME = 'upload_progress.name';
	const UPLOAD_PROGRESS_FREQ = 'upload_progress.freq';
	const UPLOAD_PROGRESS_MIN_FREQ = 'upload_progress.min_freq';
	const LAZY_WRITE = 'session.lazy_write';

}