<?php
/*
 * This file is part of the phpCyberPlusPaiement lib.
 *
 * (c) Pierre Tachoire <pierre.tachoire@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class CybPP_ResponseTest
extends PHPUnit_Framework_TestCase
{
	
	public function testSignature() {	
		
		$query = new CybPP_Response();
		$query->setVersion('V1');
		$query->setSiteId( '12345678' );
		$query->setCtxMode( 'TEST' );
		$query->setTransId( '654321' );
		$query->setTransDate( '20090501193530' );
		$query->setValidationMode( '1' );
		$query->setCaptureDelay( '3' );
		$query->setPaymentConfig( 'SINGLE' );
		
		$query->setCardBrand( 'VISA' );
		$query->setCardNumber( '123456789' );
		$query->setAmount( '1524' );
		$query->setCurrency( '978' );
		
		$query->setAuthMode('MARK');
		$query->setAuthResult('02');
		$query->setAuthNumber('123456');
		$query->setWarrantyResult( 'YES' );
		$query->setPaymentCertificate( '0123456789012345678901234567890123456789' );
		$query->setResult('02');		
		
		$query->setCertificat( '1122334455667788' );
		
		$base_sha1 = sha1( 'V1+12345678+TEST+654321+20090501193530+1+3+SINGLE+VISA+123456789+1524+978+MARK+02+123456+YES+0123456789012345678901234567890123456789+02+1122334455667788' );
		
		$this->assertEquals( $base_sha1, $query->getSignatureProcessed() );
		
		$query->setSignature( $base_sha1 );
		
		$this->assertTrue( $query->checkSignature() );
		
	}
	
	public function testSignatureHash() {	
		
		$query = new CybPP_Response();
		$query->setVersion('V1');
		$query->setSiteId( '12345678' );
		$query->setCtxMode( 'TEST' );
		$query->setTransId( '654321' );
		$query->setTransDate( '20090501193530' );
		$query->setValidationMode( '1' );
		$query->setCaptureDelay( '3' );
		$query->setPaymentConfig( 'SINGLE' );
		
		$query->setCardBrand( 'VISA' );
		$query->setCardNumber( '123456789' );
		$query->setAmount( '1524' );
		$query->setCurrency( '978' );
		
		$query->setAuthMode('MARK');
		$query->setAuthResult('02');
		$query->setAuthNumber('123456');
		$query->setWarrantyResult( 'YES' );
		$query->setPaymentCertificate( '0123456789012345678901234567890123456789' );
		$query->setResult('02');		
		
		$query->setCertificat( '1122334455667788' );
		
		$query->setHash( '00001111222233334455556668877' );
		
		$base_sha1 = sha1( 'V1+12345678+TEST+654321+20090501193530+1+3+SINGLE+VISA+123456789+1524+978+MARK+02+123456+YES+0123456789012345678901234567890123456789+02+00001111222233334455556668877+1122334455667788' );
		
		$this->assertEquals( $base_sha1, $query->getSignatureProcessed() );
		
		$query->setSignature( $base_sha1 );
		
		$this->assertTrue( $query->checkSignature() );
		
	}
	
}