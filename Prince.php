<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI-PrinceXML
 *
 * A lightweight library for the simplest use of PrinceXML
 * together with Codeigniter.
 *
 * @version		0.1
 * @author		Anthoni Giskegjerde (@antonigiske)
 */
class Prince
{
	/**
	 * Codeigniter Instance
	 *
	 * @var string
	 */
	private $_ci;
	
	
	
	/**
	 * Path to the Prince executable
	 *
	 * @var string
	 */
	private $_executable;
	
	
	
	/**
	 * Arguments that are being sent to Prince.
	 *
	 * @var string
	 */
	private $_args;
	
	
	
	/**
	 * Echo out error messages.
	 *
	 * @var bool
	 */
	private $_display_errors;
	
	
	
	/**
	 * Enable UTF8
	 *
	 * @var string
	 */
	private $_utf8;
	
	
	
	/**
	 * Contructor
	 *
	 * @author Anthoni Giskegjerde
	 */
	function __construct()
	{
		$this->_ci =& get_instance();
		$this->_executable = '/usr/local/bin/prince';
		$this->_args = $this->_executable . ' --server -i "html" --silent -';
		$this->_display_errors = true;
		$this->_utf8 = true;
	}
	
	
	
	/**
	 * Creates PDF raw data from HTML
	 *
	 * @param string $html 
	 * @return mixed bool string
	 * @author Anthoni Giskegjerde
	 */
	function html_to_pdf($html)
	{
		return $this->_generate_pdf($html);
	}
	
	
	
	/**
	 * Creates a PDF based on a CI view.
	 *
	 * @param string $view_path 
	 * @return void
	 * @author Anthoni Giskegjerde
	 */
	function view_to_pdf($view_path)
	{
		return $this->_generate_pdf($this->_ci->load->view($view_path, false, true));
	}
	
	
	
	/**
	 * Creates a PDF based on a template, using the CI Template Parser.
	 *
	 * @param string $view 
	 * @param array $template_vars 
	 * @return mixed bool string
	 * @author Anthoni Giskegjerde
	 */
	function template_to_pdf($template_path, $template_vars = array())
	{
		$this->_ci->load->library('parser');
		return $this->_generate_pdf($this->_ci->parser->parse($template_path, $template_vars, true));
	}
	
	
	
	/**
	 * Checks if there are any errors in the pipeline.
	 *
	 * @param resource $pipe 
	 * @return bool
	 * @author Anthoni Giskegjerde
	 */
	function _check_for_errors($pipe)
	{
		while (!feof($pipe))
		{
		    $line = fgets($pipe);

		    if($line)
		    {
				$tag = substr($line, 0, 3);
				$body = substr($line, 4);

				if($tag == 'fin') return false;

				$messages[] = $body;
			}
		}
		
		if($this->_display_errors) echo 'Prince error: ' . implode(', ', $messages);
		
		return true;
	}
	
	
	
	/**
	 * Generates a PDF from HTML
	 *
	 * @param string $html 
	 * @return mixed bool string
	 * @author Anthoni Giskegjerde
	 */
	function _generate_pdf($html)
	{
		$html = ($this->_utf8) ? utf8_decode($html) : $html;
		
		$descriptorspec = array(
		    0 => array("pipe", "r"),
		    1 => array("pipe", "w"),
		    2 => array("pipe", "w")
	    );
	
		if(!is_resource($process = proc_open($this->_args, $descriptorspec, $pipes))) return false;
		
		fwrite($pipes[0], $html);
		fclose($pipes[0]);

		$raw_data = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		$error_messages = $this->_check_for_errors($pipes[2]);
		fclose($pipes[2]);

		proc_close($process);
		
		return ($error_messages) ? false : $raw_data;
	}
}