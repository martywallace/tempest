<?php namespace Tempest\Services;

use Tempest\Utils\StringUtil;


/**
 * Assists with extracting information about the client's browser and platform.
 *
 * @author Marty Wallace.
 */
class PlatformService extends \Browser implements IService
{

	/**
	 * Returns a list of classes for adding to the <html> element on the page.
	 *
	 * @return array
	 */
	public function getClasses()
	{
		return implode(' ', array(
			// Which browser?
			StringUtil::hyphenate('browser-' . $this->getBrowser()),
			StringUtil::hyphenate('version-' . StringUtil::firstPart($this->getVersion(), '.')),

			// Which platform?
			StringUtil::hyphenate('platform-' . $this->getPlatform()),

			// Other checks.
			StringUtil::hyphenate(($this->isMobile() ? 'mobile' : 'not-mobile')),
			StringUtil::hyphenate(($this->isTablet() ? 'tablet' : 'not-tablet')),
			StringUtil::hyphenate(($this->isFacebook() ? 'facebook' : 'not-facebook'))
		));
	}


	/**
	 * Whether or not this service is accessible within Twig templates.
	 *
	 * @return bool
	 */
	public function isTwigAccessible() { return true; }

}