<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die;

if (defined('_HANDOUT_config')) {
	return true;
} else {
	define('_HANDOUT_config', 1);
}

class HANDOUT_Config {
	/**
	* *
	*
	* @var string The path to the configuaration file
	*/
	var $_path = null;

	/**
	* *
	*
	* @var string The name of the configuaration class
	*/
	var $_name = null;

	/**
	* *
	*
	* @var object An object of configuration variables
	*/
	var $_config = null;

	function HANDOUT_Config($name, $path)
	{
		$this->_path = $path;
		$this->_name = $name;
		$this->_loadConfig();
	}

	/**
	*
	* @param string $ The name of the variable
	* @return mixed The value of the configuration variable or null if not found
	*/
	function getCfg($varname , $default = null)
	{
		if (isset($this->_config->$varname)) {
			return $this->_config->$varname;
		} else {
			if (! is_null($default)) {
				$this->_config->$varname = $default;
			}
			return $default;
		}
	}


	/**
	*
	* @param string $ The name of the variable
	* @param string $ The new value of the variable
	* @return bool True if succeeded, otherwise false.
	*/
	function setCfg($varname, $value, $create = false)
	{
		if ($create || isset($this->_config->$varname)) {
			$this->_config->$varname = $value;
			return true;
		} else {
			return false;
		}
	}

	/**
	* Loads the configuration file and creates a new class
	*/
	function _loadConfig()
	{
		if (file_exists($this->_path)) {
			require_once $this->_path;
			if( class_exists($this->_name)) {
				$this->_config = new $this->_name();
				if (!is_subclass_of($this->_config, 'JObject')) {
					$config = new JObject();
					$config->setProperties(get_object_vars($this->_config));
					$this->_config =& $config;
				}
			}
		}
		// Load default configuration
		if (!$this->_config) {
			require_once JPATH_ADMINISTRATOR . "/components/com_handout/handout.config.dist.php";
			$this->_config = new HandoutConfig();
		}
	}

	/**
	* Saves the configuration object
	*/
	function saveConfig()
	{
		$user = &JFactory::getUser();

		$this->check();

		$config = "<?php\n";
		$config .= "if(defined('_" . $this->_name . "')) {\nreturn true;\n} else { \ndefine('_" . $this->_name . "',1); \n\n";
		$config .= "class " . $this->_name . " extends JObject\n{\n";
		$config .= "// Last Edit: " . strftime("%a, %Y-%b-%d %R") . "\n";
		$config .= "// Edited by: " . $user->username . "\n";

		$vars = get_object_vars($this->_config);
		ksort($vars);
		foreach($vars as $key => $value) {
			if ($key[0] == '_') continue;
			$config .= 'var $' . $key . ' = ' . var_export($value , true) . ";\n" ;
		}

		$config .= "}\n}";
		jimport ('joomla.filesystem.file');
		return JFile::write($this->_path, $config);
	}

	/**
	*
	* @return object The configuration object
	*/
	function getAllCfg()
	{
		return $this->_config;
	}

	function check(){
		/**
		 * Handle single and double quotes
		 */
		$search  = array( "\\'", '\"' );
		$replace = array( "'", '"' );

		$vars = get_object_vars($this->_config);
		foreach( $vars as $key=>$var ) {
			if ($key[0] == '_') continue;
			$this->_config->$key = str_replace( $search, $replace, $var );
		}


		return true;
	}
}