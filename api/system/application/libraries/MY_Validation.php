<?php
class MY_Validation extends CI_Validation {
	public function __construct() {
		parent::CI_Validation();
		$this->_error_prefix = "";
		$this->_error_suffix = "";
	}
	
    /**
     * valid_url
     *
     * @access    public
     * @param    field
     * @return    bool
     */
    function valid_url($field)
    {
        
        $this->CI->validation->set_message('valid_url', 'The %s field must contain a valid url.');

        return !preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $field) ? FALSE : TRUE;
    }
    
    /**
     * base64されたURLかどうか判定する。
     * @param $field
     */
    function valid_base64_url($field) {
    	if (!$this->valid_base64($field)) return false;
    	return $this->valid_url(base64_decode($field));
    }
    
	/**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @return	bool
	 */		
	function run()
	{
		$validation_args = $this->CI->method_args();
		
		// Do we even have any data to process?  Mm?
		if (!is_array($validation_args) OR count($this->_rules) == 0)
		{
			return FALSE;
		}
	
		// Load the language file containing error messages
		$this->CI->lang->load('validation');
							
		// Cycle through the rules and test for errors
		foreach ($this->_rules as $field => $rules)
		{
			//Explode out the rules!
			$ex = explode('|', $rules);

			// Is the field required?  If not, if the field is blank  we'll move on to the next test
			if ( ! in_array('required', $ex, TRUE))
			{
				if ( ! isset($validation_args[$field]) OR $validation_args[$field] == '')
				{
					continue;
				}
			}
			
			/*
			 * Are we dealing with an "isset" rule?
			 *
			 * Before going further, we'll see if one of the rules
			 * is to check whether the item is set (typically this
			 * applies only to checkboxes).  If so, we'll
			 * test for it here since there's not reason to go
			 * further
			 */
			if ( ! isset($validation_args[$field]))
			{			
				if (in_array('isset', $ex, TRUE) OR in_array('required', $ex))
				{
					if ( ! isset($this->_error_messages['isset']))
					{
						if (FALSE === ($line = $this->CI->lang->line('isset')))
						{
							$line = 'The field was not set';
						}							
					}
					else
					{
						$line = $this->_error_messages['isset'];
					}
					
					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$message = sprintf($line, $mfield);

					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;
					$this->_error_array[] = $message;
				}
						
				continue;
			}
	
			/*
			 * Set the current field
			 *
			 * The various prepping functions need to know the
			 * current field name so they can do this:
			 *
			 * $validation_args[$this->_current_field] == 'bla bla';
			 */
			$this->_current_field = $field;

			// Cycle through the rules!
			foreach ($ex As $rule)
			{
				// Is the rule a callback?			
				$callback = FALSE;
				if (substr($rule, 0, 9) == 'callback_')
				{
					$rule = substr($rule, 9);
					$callback = TRUE;
				}
				
				// Strip the parameter (if exists) from the rule
				// Rules can contain a parameter: max_length[5]
				$param = FALSE;
				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
				{
					$rule	= $match[1];
					$param	= $match[2];
				}
				
				// Call the function that corresponds to the rule
				if ($callback === TRUE)
				{
					if ( ! method_exists($this->CI, $rule))
					{ 		
						continue;
					}
					
					$result = $this->CI->$rule($validation_args[$field], $param);	
					
					// If the field isn't required and we just processed a callback we'll move on...
					if ( ! in_array('required', $ex, TRUE) AND $result !== FALSE)
					{
						continue 2;
					}
					
				}
				else
				{				
					if ( ! method_exists($this, $rule))
					{
						/*
						 * Run the native PHP function if called for
						 *
						 * If our own wrapper function doesn't exist we see
						 * if a native PHP function does. Users can use
						 * any native PHP function call that has one param.
						 */
						if (function_exists($rule))
						{
							$validation_args[$field] = $rule($validation_args[$field]);
							$this->$field = $validation_args[$field];
						}
											
						continue;
					}
					
					$result = $this->$rule($validation_args[$field], $param);
				}
								
				// Did the rule test negatively?  If so, grab the error.
				if ($result === FALSE)
				{
					if ( ! isset($this->_error_messages[$rule]))
					{
						if (FALSE === ($line = $this->CI->lang->line($rule)))
						{
							$line = 'Unable to access an error message corresponding to your field name.';
						}						
					}
					else
					{
						$line = $this->_error_messages[$rule];
					}				

					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$mparam = ( ! isset($this->_fields[$param])) ? $param : $this->_fields[$param];
					$message = sprintf($line, $mfield, $mparam);
					
					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;

					// Add the error to the error array
					$this->_error_array[] = $message;				
					continue 2;
				}				
			}
			
		}
		
		$total_errors = count($this->_error_array);

		/*
		 * Recompile the class variables
		 *
		 * If any prepping functions were called the $validation_args data
		 * might now be different then the corresponding class
		 * variables so we'll set them anew.
		 */	
		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}
		
		$this->set_fields();

		// Did we end up with any errors?
		if ($total_errors == 0)
		{
			return TRUE;
		}
		
		// Generate the error string
		foreach ($this->_error_array as $val)
		{
			$this->error_string .= strlen($this->error_string)>0 ? "\n" : "";
			$this->error_string .= $this->_error_prefix.$val.$this->_error_suffix;
		}

		return FALSE;
	}
}
?>