<?php

/*
 * This file is part of the phpCyberPlusPaiement lib.
 *
 * (c) Pierre Tachoire <pierre.tachoire@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * CybPP_Exception
 *
 */
class CybPP_Exception extends Exception {
	
	/**
	 * 
	 * @param unknown_type $message
	 * @param unknown_type $code
	 * @return unknown_type
	 */
	public function __construct( $message=null, $code=null ) {		
		parent::__construct( $message, $code );		
	}
	
}