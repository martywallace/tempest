<?php namespace Tempest\Templating;

use Tempest\Templating\Template;
use Tempest\Templating\Hooks;


/**
 * A Token found within a Template.
 * @author Marty Wallace.
 */
class Token
{

	const B_HTML_ESCAPE = '!';
	const B_REPLACE_NULL_WITH_EMPTY = '?';
	const B_REMOVE_WITHOUT_VALUE = '*';


	private $base;
	private $behaviour;
	private $context;
	private $parts;
	private $behaviours;
	private $hooks;


	/**
	 * Constructor.
	 * @param $stack The match stack provided by <code>preg_match()</code>.
	 * @param $index The index of this Token within the match stack.
	 */
	public function __construct(Array $stack, $index)
	{
		$this->base = $stack[0][$index];
		$this->context = $stack[2][$index];
		$this->context = ltrim($this->context, '@');
		$this->behaviours = str_split($stack[1][$index]);
		$this->hooks = preg_split('/[\:\s]+/', trim($stack[4][$index], ': '));

		$parts = preg_split('/\.+/', trim($stack[3][$index], '.'));

		foreach($parts as $part)
		{
			$this->parts[] = new TokenPart($part);
		}
	}


	/**
	 * Replaces this Token within the specified value within the subject Template content.
	 * @param $subject The subject content.
	 * @param $value The replacement value.
	 */
	public function replace($subject, $value)
	{
		return str_replace($this->base, $this->toText($value), $subject);
	}


	/**
	 * Translates a replacement value to text for insertion into a Template.
	 * @param $value The replacement value.
	 */
	private function toText($value)
	{
		if(is_array($value))
		{
			$value = '';
			trigger_error("Attempted insertion of array into token <code>$this->base</code>. Did you mean to use <code>Template->batch()</code> here?");
		}

		if(is_object($value))
		{
			if(method_exists($value, '__toString')) $value = $value->__toString();
			else
			{
				$value = '';
				trigger_error("Attempted insertion of <code>" . get_class($value) . "</code> into token <code>$this->base</code> where a <code>__toString()</code> method was not defined.");
			}
		}

		if($value === true) $value = 'true';
		if($value === false) $value = 'false';
		if($value === null) $value = 'null';


		// The value needs to be escaped.
		if($this->hasPrefix(self::B_HTML_ESCAPE)) $value = htmlspecialchars($value);


		// The value should be interpreted as an empty string if it is null or empty.
		if($this->hasPrefix(self::B_REPLACE_NULL_WITH_EMPTY))
		{
			if($value === 'null') return '';
		}


		// Apply hooks.
		foreach($this->hooks as $hook)
		{
			if(strlen($hook) === 0) continue;

			if(method_exists('Tempest\Templating\Hooks', $hook)) $value = Hooks::$hook($value);
			else trigger_error("Hook <code>$hook</code> not defined.");
		}


		return $value;
	}


	/**
	 * Determine whether this Token has a given prefix.
	 * @param $prefix The prefix to check.
	 */
	public function hasPrefix($prefix)
	{
		return in_array($prefix, $this->behaviours);
	}


	/**
	 * Determine whether this Token is associated with a specific context.
	 */
	public function isContextual(){ return strlen($this->context) > 0; }


	/**
	 * Returns the base matched token value.
	 */
	public function getBase(){ return $this->base; }


	/**
	 * Returns the associated context, if any.
	 */
	public function getContext(){ return $this->context; }


	/**
	 * Returns an Array containing each part of this Token.
	 */
	public function getParts(){ return $this->parts; }

}