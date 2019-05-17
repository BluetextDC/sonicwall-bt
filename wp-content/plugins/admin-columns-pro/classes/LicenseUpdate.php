<?php
namespace ACP;

use ACP\License\API;
use ACP\License\Request;

class LicenseUpdate {

	/** @var License */
	private $license;

	/** @var API */
	private $api;

	public function __construct( License $license, API $api ) {
		$this->license = $license;
		$this->api = $api;
	}

	public function update() {
		$request = new Request( array(
			'request'     => 'licensedetails',
			'license_key' => $this->license->get_key(),
		) );

		$response = $this->api->request( $request );

		if ( $response->get( 'expiry_date' ) ) {
			$this->license->set_expiry_date( $response->get( 'expiry_date' ) );
		}

		if ( $response->get( 'renewal_discount' ) ) {
			$this->license->set_renewal_discount( $response->get( 'renewal_discount' ) );
		}

		if ( $response->get( 'status' ) ) {
			$this->license->set_status( $response->get( 'status' ) );
		}

		$this->license->save();
	}

}