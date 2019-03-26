<?php

	/**
	 * summary
	 */

	class SecurityPlugin
	{

		/**
	 	 * summary
	 	 */		
		public function escapeInput($input = NULL)
		{
			if (is_array($input)) {
				return array_map([$this, 'escapeInput'], $input);
			}
			
			$input = (string) $input;
			
			$input = strip_tags($input);
			$input = htmlspecialchars($input);
			$input = addslashes($input);
			
			return preg_replace('/(^\s+)|(\s+$)/su', '', $input);
		}
	}
?>