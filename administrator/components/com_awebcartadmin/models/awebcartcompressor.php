<?php
define('_AWEB_CART_ADMIN','ok');

class awebcartcompressor
{
	public function hasbz()
	{
		if (function_exists('bzcompress')) return 1;
		else return 0;
	}

	public function compress($data)
	{
		if (function_exists('bzcompress')) return base64_encode(bzcompress($data,9));
		else return $data;
	}
	public function decompress($data)
	{
		if (function_exists('bzcompress')) return bzdecompress(base64_decode($data));
		else return $data;
	}
}

?>