<?php
/**
 * Export File model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: exportfile.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

/**
 * Export File Model
 */
class CsviModelExportfile extends JModelLegacy {

	/** @var string Field delimiter */
	private $_field_delim = null;
	/** @var string Text delimiter */
	private $_text_delim = null;
	/** @var string Category separator */
	private $_catsep = null;
	/** @var array Holds the data for combined fields */
	private $_outputfield = array();
	/** @var string Contains the header name to be added */
	private $_headername = null;
	/** @var string Contains the last field to be exported */
	private $_last_field = null;
	/** @var string Contains the last field to be exported */
	private $_contents = array();

	/**
	 * Prepare for export
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo		Update the output type to include multiple destinations
	 * @todo		Is the setting of the export_type in JRequest necessary?
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getPrepareExport() {
		// Load the form handler
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$option = $jinput->get('option');
		$data	= $jinput->get('jform', array(), 'array');

		// Prepare the template
		$template = $jinput->get('template', null, null);
		if (is_null($template)) {
			$data['id'] = $jinput->get('template_id', 0, 'int');
			$template = new CsviTemplate($data);
			$jinput->set('template', $template);
		}
		$template->setName($jinput->get('template_name', '', 'string'));

		// Set the export type
		$jinput->set('export_type', $data['options']['operation']);

		// Initiate the log
		$csvilog = new CsviLog();

		// Create a new Import ID in the logger
		$csvilog->setId();

		// Set to collect debug info
		$csvilog->setDebug($template->get('collect_debug_info', 'general'));

		// Set some log info
		$csvilog->SetAction('export');
		$csvilog->SetActionType($jinput->get('export_type'), $template->getName('template_name'));

		// Add the logger to the registry
		$jinput->set('csvilog', $csvilog);

		// Load the fields to export
		$exportfields = $this->getExportFields();
		if (!empty($exportfields)) {
			// Set the last export field
			$jinput->set('export.fields', $exportfields);

			// Allow big SQL selects
			$db->setQuery("SET OPTION SQL_BIG_SELECTS=1");
			$db->query();

			// Get the filename for the export file
			$jinput->set('export.filename', $this->exportFilename());

			// See if we need to get an XML/HTML class
			$export_format = $template->get('export_file', 'general');
			if ($export_format == 'xml' || $export_format == 'html') {
				$exportclass = $this->getExportClass();
				if ($exportclass) $jinput->set('export.class', $exportclass);
				else {
					$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_EXPORT_CLASS'));
					$app->enqueueMessage(JText::_('COM_CSVI_NO_EXPORT_CLASS'), 'error');
					$jinput->set('logcount', 0);
					return false;
				}
			}

			// Return all is good
			return true;
		}
		else {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_EXPORT_FIELDS'));
			$app->enqueueMessage(JText::_('COM_CSVI_NO_EXPORT_FIELDS'), 'error');
			$jinput->set('logcount', 0);
			return false;
		}
	}

	/**
	 * Set the delimiters
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _setDelimiters() {
		if (is_null($this->_field_delim)) {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			// Set the delimiters
			$this->_field_delim = $template->get('field_delimiter', 'general', ',');
			$this->_text_delim = $template->get('text_enclosure', 'general', '');
		}
	}

	/**
	 * Process the export data
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getProcessData() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$csvilog = $jinput->get('csvilog', null, null);
		$export_format = $template->get('export_file', 'general');
		$export_fields = $jinput->get('export.fields', null, null);
		$export_class = $jinput->get('export.class', null, null);

		// Write out some import settings
		$this->_exportDetails();

		// Start the export
		if (!$this->_outputStart()) {
			// Store the log results
			$log_model = $this->_getModel('log');
			$log_model->getStoreLogResults();
			return false;
		}

		// Add signature for Excel
		if ($template->get('signature', 'general')) $this->_contents['signature'] = "\xEF\xBB\xBF";

		// Add header for XML
		if ($export_format == 'xml') $this->_contents[] = $export_class->HeaderText();
		// Add header for HTML
		else if ($export_format == 'html') {
			$this->_contents[] = $export_class->HeaderText();
			if ($template->get('include_column_headers', 'general')) {
				$this->_contents[] = $export_class->StartTableHeaderText();
				foreach ($export_fields as $column_id => $field) {
					if ($field->process && !$template->isCombine($field->field_id)) {
						$header = ($field->column_header) ? $field->column_header : $field->field_name;
						$this->_contents[] = $export_class->TableHeaderText($header);
					}
				}
				$this->_contents[] = $export_class->EndTableHeaderText();
			}
			$this->_contents[] = $export_class->BodyText();
		}
		else {
			// Add the header from the template
			$header = $template->get('header', 'layout', false);
			if ($header) {
				$this->_contents[] = $header;
				$this->writeOutput();
			}

			// Get the delimiters
			// See if the user wants column headers
			// Product type names export needs to be excluded here otherwise the column headers are incorrect
			if ($template->get('include_column_headers', 'general', true)) {
				$this->_setDelimiters();
				foreach ($export_fields as $column_id => $field) {
					if ($field->process && !$template->isCombine($field->field_id)) {
						$header = (empty($field->column_header)) ? $field->field_name : $field->column_header;
						$this->_contents[] = $this->_text_delim.$header.$this->_text_delim;
					}
				}
			}
		}

		// Output content
		$this->writeOutput();

		// Start the export from the chosen template type
		$exportmodel = $this->_getModel($jinput->get('export_type'));
		$exportmodel->getStart();

		if ($export_format == 'xml' || $export_format == 'html') {
			$footer = $export_class->FooterText();
		}
		else {
			// Add the footer from the template
			$footer = $template->get('footer', 'layout');
		}

		// Write the footer
		if ($footer && !empty($footer)) {
			$this->_contents[] = $footer;
			$this->writeOutput();
		}

		// End the export
		$this->_outputEnd();

		// Store the log results
		$log_model = $this->_getModel('log');
		$log_model->getStoreLogResults();

		// Process some settings
		switch ($template->get('exportto', 'general')) {
			case 'tofile':
			case 'toemail':
			case 'toftp':
				if (!$jinput->get('cron', false, 'bool')) {
					$app = JFactory::getApplication();
					$app->redirect(JURI::root().'administrator/index.php?option=com_csvi&task=process.finished&run_id='.$csvilog->getId());
				}
				break;
			case 'todownload':
				jexit();
				break;
			case 'tofront':
				return true;
				break;
		}
	}

	/**
	 * Cleanup after export
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	public function getCleanSession() {
		// Store the log results first
		$log = $this->_getModel('log');
		$log->getStoreLogResults();
	}

	/**
	 * Load the export class that handles the file export
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		bool true when xml class is found|false when when no site is given
	 * @since 		3.0
	 */
	public function getExportClass() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportclass = false;
		$exporttype = $template->get('export_file', 'general');
		$exportsite = $template->get('export_site', 'general', 'csvimproved');

		// Construct the file name
		$filename = $exportsite.'.php';

		// Find the export class
		$helper = JPath::find(array(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/export/'.$exporttype), $filename);
		if (!$helper) return false;
		else {
			// Load the file and instantite it
			include_once($helper);
			$classname = 'Csvi'.ucfirst($exportsite);
			$exportclass = new $classname;
		}
		return $exportclass;
	}

	/**
	 * Get the export filename
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		string	Returns the filename of the exported file
	 * @since 		3.0
	 */
	public function exportFilename() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);

		// Check if the export is limited, if so add it to the filename
		// Check if both values are greater than 0
		if (($template->get('recordstart', 'general') > 0) && ($template->get('recordend', 'general') > 0)) {
			// We have valid limiters, add the limit to the filename
			$filelimit = "_".$template->get('recordend', 'general').'_'.($template->get('recordend', 'general')-1)+$template->get('recordstart', 'general');
		}
		else $filelimit = '';

		// Set the filename to use for export
		$export_filename = trim($template->get('export_filename', 'general'));
		$local_path = JPath::clean($template->get('localpath', 'general'), '/');
		$export_file = $template->get('export_file', 'general');

		// Do some customizing
		// Replace year
		$export_filename = str_replace('[Y]', date('Y', time()), $export_filename);
		$export_filename = str_replace('[y]', date('y', time()), $export_filename);
		// Replace month
		$export_filename = str_replace('[M]', date('M', time()), $export_filename);
		$export_filename = str_replace('[m]', date('m', time()), $export_filename);
		$export_filename = str_replace('[F]', date('F', time()), $export_filename);
		$export_filename = str_replace('[n]', date('n', time()), $export_filename);
		// Replace day
		$export_filename = str_replace('[d]', date('d', time()), $export_filename);
		$export_filename = str_replace('[D]', date('D', time()), $export_filename);
		$export_filename = str_replace('[j]', date('j', time()), $export_filename);
		$export_filename = str_replace('[l]', date('l', time()), $export_filename);
		// Replace hour
		$export_filename = str_replace('[g]', date('g', time()), $export_filename);
		$export_filename = str_replace('[G]', date('G', time()), $export_filename);
		$export_filename = str_replace('[h]', date('h', time()), $export_filename);
		$export_filename = str_replace('[H]', date('H', time()), $export_filename);
		// Replace minute
		$export_filename = str_replace('[i]', date('i', time()), $export_filename);
		// Replace seconds
		$export_filename = str_replace('[s]', date('s', time()), $export_filename);

		// Setup the full path for the filename
		switch ($template->get('exportto', 'general')) {
			case 'toemail':
			case 'toftp':
				if (!empty($export_filename)) $localfile = CSVIPATH_TMP.'/'.$export_filename;
				else $localfile = CSVIPATH_TMP.'/CSVI_VM_'.$jinput->get('template_name', '', 'cmd').'_'.date("j-m-Y_H.i").$filelimit.".".$export_file;
				break;
			case 'tofile':
				if (!empty($local_path) && !empty($export_filename)) $localfile = $local_path.'/'.$export_filename;
				else if (!empty($local_path))  $localfile = $local_path.'/CSVI_VM_'.$jinput->get('template_name', '', 'cmd').'_'.date("j-m-Y_H.i").$filelimit.".".$export_file;
				else 'CSVI_VM_'.$jinput->get('template_name', '', 'cmd').'_'.date("j-m-Y_H.i").$filelimit.".".$export_file;
				break;
			case 'tofront':
				$uri = JURI::getInstance();
				$localfile = $uri->toString();
				break;
			default:
				if (!empty($export_filename)) $localfile = $export_filename;
				else $localfile = 'CSVI_VM_'.$jinput->get('template_name', '', 'cmd').'_'.date("j-m-Y_H.i").$filelimit.".".$export_file;
				break;
		}

		// Clean up
		$localfile = JPath::clean($localfile, '/');

		// Set the filename
		$csvilog->setFilename($localfile);

		// Return the filename
		return $localfile;
	}

	/**
	 * Get the fields to use for the export
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		array 	Returns an array of required fields and default field values
	 * @since 		3.0
	 */
	public function getExportFields() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$template = $jinput->get('template', null, null);

		// Get the field configuration
		$export_fields = $template->get('fields');
		if (!is_array($export_fields)) $export_fields = array();

		// Return the required and default values
		return $export_fields;
	}

	/**
	 * Print out export details
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _exportDetails() {
		$jinput = JFactory::getApplication()->input;
		// Get the logger
		$csvilog = $jinput->get('csvilog', null, null);
		// Get the template settings to see if we need a preview
		$template = $jinput->get('template', null, null);

		$csvilog->addDebug(JText::_('COM_CSVI_CSVI_VERSION_TEXT').JText::_('COM_CSVI_CSVI_VERSION'));
		if (function_exists('phpversion')) $csvilog->addDebug(JText::sprintf('COM_CSVI_PHP_VERSION', phpversion()));

		// Push out all settings
		$settings = $template->getSettings();
		$this->_processSettings($settings);

		// Exporting fields
		$export_fields = $jinput->get('export.fields', null, null);
		foreach ($export_fields as $column_id => $field) {
			if ($field->process && !$template->isCombine($field->field_id)) {
				$header = (empty($field->column_header)) ? $field->field_name : $field->column_header;
				$csvilog->addDebug(JText::sprintf('COM_CSVI_DEBUG_EXPORT_FIELD', $header));
			}
		}

	}

	/**
	 * Add all the settings to the debug log
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		$data	array or object of data
	 * @return
	 * @since 		5.3
	 */
	private function _processSettings($data) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		foreach ($data as $name => $value) {
			switch ($name) {
				case 'fields':
					break;
				default:
					if (is_object($value) || is_array($value)) $this->_processSettings($value);
					else {
						switch ($name) {
							case 'ftpusername':
							case 'ftppass':
							case 'export_email_addresses':
							case 'export_email_addresses_cc':
							case 'export_email_addresses_bcc':
								break;
							default:
								switch ($value) {
									case '0':
										$value = JText::_('JNO');
										break;
									case '1':
										$value = JText::_('JYES');
										break;
								}
								$csvilog->addDebug($name.': '.$value);
								break;
						}
					}
					break;
			}
		}
	}

	/**
	 * Output the collected data
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		void
	 * @since		3.0
	 */
	private function _outputStart() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportfilename = $jinput->get('export.filename', null, 'string');
		$result = false;
		if ($template->get('use_system_limits', 'limit')) {
			$csvilog->addDebug('Setting system limits:');
			// Apply the new memory limits
			$csvilog->addDebug('Setting max_execution_time to '.$template->get('max_execution_time', 'limit').' seconds');
			@ini_set('max_execution_time', $template->get('max_execution_time', 'limit'));
			$csvilog->addDebug('Setting memory_limit to '.$template->get('memory_limit', 'limit').'M');
			if ($template->get('memory_limit', 'limit') == '-1') {
				$csvilog->addDebug('Setting memory_limit to '.$template->get('memory_limit', 'limit'));
				@ini_set('memory_limit', $template->get('memory_limit', 'limit'));
			}
			else {
				$csvilog->addDebug('Setting memory_limit to '.$template->get('memory_limit', 'limit').'M');
				@ini_set('memory_limit', $template->get('memory_limit', 'limit').'M');
			}
		}
		switch ($template->get('exportto', 'general', 'todownload')) {
			case 'todownload':
				if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
					$UserBrowser = "Opera";
				}
				elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
					$UserBrowser = "IE";
				} else {
					$UserBrowser = '';
				}
				$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';

				// Clean the buffer
				while( @ob_end_clean() );

				header('Content-Type: ' . $mime_type);
				header('Content-Encoding: UTF-8');
				header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

				if ($UserBrowser == 'IE') {
					header('Content-Disposition: inline; filename="'.$exportfilename.'"');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
				} else {
					header('Content-Disposition: attachment; filename="'.$exportfilename.'"');
					header('Pragma: no-cache');
				}
				$result = true;
				break;
			case 'tofile':
				jimport('joomla.filesystem.folder');

				// Check if the folder exists
				if (!JFolder::exists(dirname($exportfilename))) {
					if (!JFolder::create(dirname($exportfilename))) {
						$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_CREATE_FOLDER', dirname($exportfilename)));
						$result = false;
					}
				}

				// open the file for writing
				$handle = @fopen($exportfilename, 'w+');
				if (!$handle) {
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_OPEN_FILE', $exportfilename));
					$result = false;
				}
				// Let's make sure the file exists and is writable first.
				if (is_writable($exportfilename)) {
				    $jinput->set('handle', $handle);
				    $result = true;
				}
				else {
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_WRITE_FILE', $exportfilename));
					$result = false;
				}
				break;
			case 'toftp':
			case 'toemail':
				// open the file for writing
				$handle = fopen($exportfilename, 'w+');
				if (!$handle) {
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_OPEN_FILE', $exportfilename));
					$result = false;
				}
				// Let's make sure the file exists and is writable first.
				if (is_writable($exportfilename)) {
				    $jinput->set('handle', $handle);
				    $result = true;
				}
				else {
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_WRITE_FILE', $exportfilename));
					$result = false;
				}
				break;
			case 'tofront':
				$result = true;
				break;
		}

		return $result;
	}

	/**
	 * Write the output to download or to file
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		string	$contents	the content to output
	 * @return 		bool	true if data is output | false if data is not output
	 * @since 		3.0
	 */
	protected function writeOutput() {
		// Let's take the local contents if nothing is supplied
		$contents = $this->_contents;

		// Clean the local contents
		$this->_contents = array();

		if (!empty($contents)) {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			$template = $jinput->get('template', null, null);
			$exportfilename = $jinput->get('export.filename', null, 'string');

			if (!is_array($contents)) $contents = (array) $contents;

			switch ($template->get('exportto', 'general')) {
				case 'todownload':
				case 'tofront':
					if (isset($contents['signature'])) {
						echo $contents['signature'];
						unset($contents['signature']);
					}
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') {
						echo implode("", $contents)."\r\n";
					}
					else {
						echo implode($this->_field_delim, $contents)."\r\n";
					}
					break;
				case 'tofile':
				case 'toftp':
				case 'toemail':
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') {
						$writedata = '';
						if (isset($contents['signature'])) {
							$writedata = $contents['signature'];
							unset($contents['signature']);
						}
						$writedata .= implode('', $contents);
						if (fwrite($jinput->get('handle', null, null), $writedata."\r\n") === FALSE) {
							$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_WRITE_FILE', $exportfilename));
					   		return false;
						}
					}
					else {
						if (fwrite($jinput->get('handle', null, null), implode($this->_field_delim, $contents)."\r\n") === FALSE) {
							$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CANNOT_WRITE_FILE', $exportfilename));
					   		return false;
						}
					}
					break;
			}
		}
		return true;
	}

	/**
	 * Finalize export output
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	private function _outputEnd() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportfilename = $jinput->get('export.filename', null, 'string');

		jimport('joomla.filesystem.file');
		switch ($template->get('exportto', 'general')) {
			case 'todownload':
				break;
			case 'tofile':
				$csvilog->AddStats('information', JText::sprintf('COM_CSVI_EXPORTFILE_CREATED', $exportfilename));
				fclose($jinput->get('handle', null, null));
				break;
			case 'toftp':
				// Close the file handle
				fclose($jinput->get('handle', null, null));

				// Start the FTP
				jimport('joomla.client.ftp');
				$ftp = JFTP::getInstance($template->get('ftphost', 'general', '', 'string'), $template->get('ftpport', 'general'), null, $template->get('ftpusername', 'general', '', 'string'), $template->get('ftppass', 'general', '', 'string'));
				$ftp->chdir($template->get('ftproot', 'general', '/', 'string'));
				$ftp->store($exportfilename);
				$ftp->quit();

				// Remove the temporary file
				JFile::delete($exportfilename);

				$csvilog->AddStats('information', JText::sprintf('COM_CSVI_EXPORTFILE_CREATED', $exportfilename));
				break;
			case 'toemail':
				fclose($jinput->get('handle', null, null));
				$this->_getMailer();
				// Add the email address
				$addresses = explode(',', $template->get('export_email_addresses', 'email'));
				foreach ($addresses as $address) {
					if (!empty($address)) $this->mailer->AddAddress($address);
				}
				$addresses_cc = explode(',', $template->get('export_email_addresses_cc', 'email'));
				if (!empty($addresses_cc)) {
					foreach ($addresses_cc as $address) {
						if (!empty($address)) $this->mailer->AddCC($address);
					}
				}
				$addresses_bcc = explode(',', $template->get('export_email_addresses_bcc', 'email'));
				if (!empty($addresses_bcc)) {
					foreach ($addresses_bcc as $address) {
						if (!empty($address)) $this->mailer->AddBCC($address);
					}
				}

				// Mail submitter
				$htmlmsg = '<html><body>'.$this->_getRelToAbs($template->get('export_email_body', 'email')).'</body></html>';
				$this->mailer->setBody($htmlmsg);
				$this->mailer->setSubject($template->get('export_email_subject', 'email'));

				// Add the attachemnt
				$this->mailer->AddAttachment($exportfilename);

				// Send the mail
				$sendmail = $this->mailer->Send();
				if (is_a($sendmail, 'JException')) $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_NO_MAIL_SEND', $sendmail->getMessage()));
				else $csvilog->AddStats('information', JText::_('COM_CSVI_MAIL_SEND'));

				// Clear the mail details
				$this->mailer->ClearAddresses();

				// Remove the temporary file
				JFile::delete($exportfilename);

				$csvilog->AddStats('information', JText::sprintf('COM_CSVI_EXPORTFILE_CREATED', $exportfilename));
				break;
		}
	}

	/**
	 * Constructs a limit for a query
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		string	the limit to apply to the query
	 * @since 		3.0
	 */
	protected function getExportLimit() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$recordstart = $template->get('recordstart', 'general', 0, 'int');
		$recordend = $template->get('recordend', 'general', 0, 'int');
		$limits = array();
		$limits['offset'] = 0;
		$limits['limit'] = 0;
		// Check if the user only wants to export some products
		if ($recordstart && $recordend) {
			// Check if both values are greater than 0
			if (($recordstart > 0) && ($recordend > 0)) {
				// We have valid limiters, add the limit to the query
				// Recordend needs to have 1 deducted because MySQL starts from 0
				$limits['offset'] = $recordstart-1;
				$limits['limit'] = $recordend-$recordstart;
			}
		}
		return $limits;
	}

	/**
	 * Create an SQL filter
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		string	$filter		what kind of SQL type should be created
	 * @param 		array	$ignore		an array of fields not to process
	 * @param		array	$special	an array of special fields not to qn
	 * @return 		string	the SQL part to add to the query
	 * @since 		3.0
	 */
	protected function getFilterBy($filter, $ignore=array(), $special=array()) {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$export_fields = $jinput->get('export.fields', array(), 'array');
		$fields = array();

		// Add some basic fields never to be handled
		$ignore[] = 'custom';

		// Collect the fields to process
		foreach ($export_fields as $column_id => $field) {
			if ($field->process) {
				switch ($filter) {
					case 'groupby':
						$process = true;
						break;
					case 'sort':
						$process = $field->sort;
						break;
					default:
						$process = false;
				}
				if ($process) {
					// Check if field needs to be skipped
					if (!in_array($field->field_name, $ignore)) {
						// Check if field is special
						if (!array_key_exists($field->field_name, $special)) {
							$fields[] = $db->qn($field->field_name);
						}
						else {
							$fields[] = $special[$field->field_name];
						}
					}
				}
			}
		}

		// Construct the SQL part
		if (!empty($fields)) {
			switch ($filter) {
				case 'groupby':
					$groupby_fields = array_unique($fields);
					$q = implode(',', $groupby_fields);
					break;
				case 'sort':
					$sort_fields = array_unique($fields);
					$q = implode(', ', $sort_fields);
					break;
				default:
					$q = '';
					break;
			}
		}
		else $q = '';

		return $q;
	}

	/**
	 * Process an array of data to add to the output
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		5.0
	 */
	protected function addExportFields($record) {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$export_fields = $jinput->get('export.fields', array(), 'array');

		// Add all fields to the export
		foreach ($export_fields as $column_id => $field) {
			if ($field->process && !$template->isCombine($field->field_id)) {
				// Get the value
				if (isset($record->output[$field->field_id])) $fieldvalue = $record->output[$field->field_id];
				else $fieldvalue = '';

				// See if we need to combine
				if (is_array($field->combine) && !empty($field->combine)) {
					foreach ($field->combine as $combine_id) {
						if (isset($record->output[$combine_id])) $fieldvalue .= $field->combine_char.$record->output[$combine_id];
					}
				}
				$this->addExportField($fieldvalue, $field->field_name, $field->column_header, $field->cdata);
			}
		}
	}

	/**
	 * Add a field to the output
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		$combine 		boolean	true if the fields need to be combined
	 * @param 		$data 			string	Data to output
	 * @param 		$fieldname 		string	Name of the field currently being processed
	 * @param 		$column_header 	string	Name of the column
	 * @param 		$cdata			boolean true to add cdata tag for XML|false not to add it
	 * @return 		string containing the field for the export file
	 * @since 		3.0
	 */
	protected function addExportField($data, $fieldname, $column_header, $cdata=false) {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);

		// Load the session
		$exportclass =  $jinput->get('export.class', null, null);

		// Set the delimiters
		$this->_setDelimiters();

		// Clean up the data by removing linebreaks
		$find = array("\r\n", "\r", "\n");
		$replace = array('','','');
		$data = str_ireplace($find, $replace, $data);

		if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') {
			if (!is_null($this->_headername)) {
				$column_header = $this->_headername;
				$this->_headername = null;
			}
			$this->_contents[] = $exportclass->ContentText($data, $column_header, $fieldname, $cdata);
		}
		else {
			$data = str_replace($this->_text_delim, $this->_text_delim.$this->_text_delim, $data);
			$this->_contents[] = $this->_text_delim.$data.$this->_text_delim;
		}
	}

	/**
	 * Add data to the export content
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		string	$content	the content to export
	 * @return
	 * @since 		3.0
	 */
	protected function addExportContent($content) {
		$this->_contents[] = $content;
	}


	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @copyright
	 * @author
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$text	the text to parse for links
	 * @return 		string	the parsed text
	 * @since 		3.0
	 */
	private function _getRelToAbs($text) {
		$base = JURI::root();
  		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto)([^\"]*)\"/", '$1="$base\$2"', $text);

		return $text;
	}

	/**
	 * Initialise the mailer object to start sending mails
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _getMailer() {
		$mainframe = Jfactory::getApplication();
		jimport('joomla.mail.helper');

		// Start the mailer object
		$this->mailer = JFactory::getMailer();
		$this->mailer->isHTML(true);
		$this->mailer->From = $mainframe->getCfg('mailfrom');
		$this->mailer->FromName = $mainframe->getCfg('sitename');
		$this->mailer->AddReplyTo(array($mainframe->getCfg('mailfrom'), $mainframe->getCfg('sitename')));
	}

	/**
	 * Create a proxy for including other models
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _getModel($model) {
		return $this->getInstance($model, 'CsviModel');
	}
}