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
 * Fichier permettant de bootstraper les tests unitaires
 */
defined('APPLICATION_PATH') 
	or define('APPLICATION_PATH', dirname(__FILE__));
	
set_include_path( 
	APPLICATION_PATH . '/../lib'
	. PATH_SEPARATOR . get_include_path()
);

require_once('CybPP/Const.php');
require_once('CybPP/Exception.php');
require_once('CybPP/Query.php');
require_once('CybPP/Response.php');