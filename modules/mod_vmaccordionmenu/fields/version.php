<?php
/**
 * Element: Version
 * Displays the version check
 *
 * @package			NoNumber! Framework
 * @version			12.3.2
 *
 * @author			Peter van Westen <peter@nonumber.nl>
 * @link			http://www.nonumber.nl
 * @copyright		Copyright © 2011 NoNumber! All Rights Reserved
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Version Element
 *
 * Available extra parameters:
 * xml			The title
 * description		The description
 */
class gruzFieldVersion
{
	var $_version = '12.3.2';

	static function getXMLVersion($extension = 'gruzframework', $type = 'system', $admin = 1, $urlformat = 0)
	{
		if (!$extension) {
			$extension = 'gruzframework';
		}
		if (!$type) {
			$type = 'system';
		}
		if (!strlen($admin)) {
			$admin = 1;
		}

		switch ($type) {
			case 'component':
			case 'components':
			case 'module':
			case 'modules':
				$type .= in_array($type, array('component', 'module')) ? 's' : '';
				if ($admin) {
					$path = JPATH_ADMINISTRATOR;
				} else {
					$path = JPATH_SITE;
				}
				$path .= '/'.$type.'/'.($type == 'modules' ? 'mod_' : 'com_').$extension.'/'.($type == 'modules' ? 'mod_' : '').$extension.'.xml';
				break;
			default:
				$path = JPATH_SITE.'/plugins/'.$type.'/'.$extension.'/'.$extension.'.xml';
				break;
		}

		$version = '';
		$xml = JApplicationHelper::parseXMLInstallFile($path);
		if ($xml && isset($xml['version'])) {
			$version = trim($xml['version']);
			if ($urlformat) {
				$version = '?v='.strtolower(str_replace(array('FREE', 'PRO'), array('f', 'p'), $version));
			}
		}

		return $version;
	}

	function getInput($name, $id, $value, $params, $children, $j15 = 0)
	{
		$this->params = $params;

		$xml = $this->def('xml');
		$extension = $this->def('extension');

		$user = JFactory::getUser();
		$authorise = $j15 ? ($user->usertype == 'Super Administrator' || $user->usertype == 'Administrator') : $user->authorise('core.manage', 'com_installer');

		if (!strlen($extension) || !strlen($xml) || !$authorise) {
			return;
		}

		$version = '';
		if ($xml) {
			$xml = JApplicationHelper::parseXMLInstallFile(JPATH_SITE.'/'.$xml);
			if ($xml && isset($xml['version'])) {
				$version = $xml['version'];
			}
		}

	//$extension = 'gruzframework', $type = 'system', $admin = 1, $urlformat = 0
		$document = JFactory::getDocument();
		$css = '';
		$css .= ".version {display:block;text-align:right;color:brown;font-size:10px;}";
		$css .= ".readonly.plg-desc {font-weight:normal;}";
		$css .= "fieldset.radio label {width:auto;}";
		$document->addStyleDeclaration($css);

		return '<span class="version">'.JText::_('JVERSION').' '.$version."</span>";
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

if (version_compare(JVERSION, '1.6.0', 'l')) {
	// For Joomla 1.5
	class JElementGRUZ_Version extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'Version';

		function fetchTooltip($label, $description, &$node, $control_name, $name)
		{
			return;
		}

		function fetchElement($name, $value, &$node, $control_name)
		{
			$this->_gruzfield = new gruzFieldVersion();
			return $this->_gruzfield->getInput($control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1);
		}
	}
} else {
	// For Joomla 1.6
	jimport('joomla.form.formfield');

	class JFormFieldVersion extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'Version';

		protected function getLabel()
		{
			return ;
		}

		protected function getInput()
		{
			$this->_gruzfield = new gruzFieldVersion();
			return $this->_gruzfield->getInput($this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children());
		}
	}
}
