<?php

class SignedJSONWebToken {
	private $secret = "";
	private $method = "sha256";
	private $seperator = '.';

	public function __construct($secret, $method = 'sha256') {
		$this->secret = $secret;

		if (in_array($method, hash_algos())) {
			$this->method = $method;
		}
	}

	public function sign($string) {
		$this->makeHeader();
		$header = json_encode($this->makeHeader());
		$content = $this->base64url_encode($header).$this->seperator.$this->base64url_encode($string);
		$content .= $this->seperator.$this->base64url_encode($this->makeSignature($content));
		return $content;
	}

	public function unsign($string) {
		list($header, $content, $sig) = explode($this->seperator, $string);
		$header_arr = json_decode($this->base64url_decode($header));
		
		if ($header_arr->method == $this->method && $this->verifySignature($header.$this->seperator.$content, $this->base64url_decode($sig))) {
			return $this->base64url_decode($content);
		} else {
			return false;
		}
	}

	private function makeHeader() {
		return array('method' => $this->method);
	}

	private function makeSignature($string) {
		return hash_hmac($this->method, $string, $this->secret);
	}

	private function verifySignature($string, $sig) {
		$test_sig = $this->makeSignature($string);
		
		return $sig == $test_sig;
	}

	public function base64url_encode($data) { 
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	} 

	public function base64url_decode($data) { 
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	}
}