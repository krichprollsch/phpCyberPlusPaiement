<?php
/*
 * This file is part of the phpCyberPlusPaiement lib.
 *
 * (c) Pierre Tachoire <pierre.tachoire@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class CybPP_QueryTest
extends PHPUnit_Framework_TestCase
{
	
	public function testSignature() {	
		
		$query = new CybPP_Query();
		$query->setVersion('V1');
		$query->setSiteId( '12345678' );
		$query->setCtxMode( 'TEST' );
		$query->setTransId( '654321' );
		$query->setTransDate( '20090501193530' );
		$query->setValidationMode( '1' );
		$query->setCaptureDelay( '3' );
		$query->setPaymentConfig( 'SINGLE' );
		$query->setPaymentCards( array( 'VISA', 'MASTERCARD' ) );
		$query->setAmount( '1524' );
		$query->setCurrency( '978' );
		$query->setCertificat( '1122334455667788' );
		
		$base_sha1 = sha1( 'V1+12345678+TEST+654321+20090501193530+1+3+SINGLE+VISA;MASTERCARD+1524+978+1122334455667788' );
		
		$this->assertEquals( $base_sha1, $query->getSignature() );
		
	}
	
}