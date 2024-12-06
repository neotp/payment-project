<?
	$setup = [
	  'endpoint_url_TOKEN' => 'https://10.20.1.11:9000/api/PimCore/v1/accessToken', 
	  'ownerId' => 'c4ca4238a0b923820dcc509a6f75849b',
	  'requestUid' => '9999-9999-9999-9999',
	  'appKey' => '14746121',
	  'appSecret' => 'ab4379ca5adeeda285c805dc7922d753d7c015812081b9047feae17f3c2f8855',
	  'endpoint_url_DATA' => 'https://api-payment.sisthai.com:9000/api/PimCore/v1/getOutStanding',
	  'endpoint_url_SUBMIT' => 'https://api-payment.sisthai.com:9000/api/PimCore/v1/payment',
	];
	$secret_sis = 'denis';
	
	function fnCURL($url,$headers,$datas){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
		curl_setopt($ch,CURLINFO_HEADER_OUT, true);
		curl_setopt($ch,CURLOPT_HEADER, true);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
		
		$response = curl_exec($ch);
		// Get Detail
		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$res_header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$res_header = substr($response, 0, $res_header_size);
		$res_body = substr($response, $res_header_size);
		curl_close($ch);
		return $res_body;
	}
	
	function fnGetToken(){
		$setup = $GLOBALS['setup'];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $setup['endpoint_url_TOKEN'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array(
			'appKey' => $setup['appKey'],
			'appSecret' => $setup['appSecret']
		  ),
		  CURLOPT_HTTPHEADER => array(
			'ownerId: '.$setup['ownerId'],
			'requestUid: '.$setup['requestUid'],
			'timestamp: ' . time()
		  ),
		  CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL verification
		  CURLOPT_SSL_VERIFYPEER => 0  // For testing only, not recommended for production
		));

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			return 'cURL error: ' . curl_error($curl);
		} else {
			$responseData = json_decode($response, true); // Decode JSON response to array
			if (isset($responseData['data']['accessToken'])) {
				return $responseData['data']['accessToken']; // Display accessToken only
			} else {
				return "Access token not found in the response.";
			}
		}

		curl_close($curl);
	}
	
	function fnGetOutStanding($cuscode, $authorization){
	$setup = $GLOBALS['setup'];
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $setup['endpoint_url_DATA'],
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => array(
		'customerCode' => $cuscode,
	  ),
	  CURLOPT_HTTPHEADER => array(
		'ownerId: '.$setup['ownerId'],
		'requestUid: '.$setup['requestUid'],
		'timestamp: ' . time(),
		'authorization: Bearer '.$authorization
	  ),
	  CURLOPT_SSL_VERIFYHOST => 0, 
	  CURLOPT_SSL_VERIFYPEER => 0  
	));

	$response = curl_exec($curl);
	if (curl_errno($curl)) {
		return ['error' => 'cURL error: ' . curl_error($curl)];
	}

		curl_close($curl);

		$responseData = json_decode($response, true); 

		if (isset($responseData['data']) && is_array($responseData['data'])) {
			return $responseData['data'];
		} else {
			return [];
		}
	
	}
	
	function fnPayment($cuscode, $billing, $bankCode, $authorization){
		$setup = $GLOBALS['setup'];
		$curl = curl_init();

		$data = array(
			'customerCode' => $cuscode,
			'bankCode' => $bankCode,
			'billing' => $billing,
			'urlCallBack' => 'http://www3.sisthai.com/SiSPayment/index.php'          
		);
		$postData = json_encode($data, true);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $setup['endpoint_url_SUBMIT'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $postData,
		  CURLOPT_HTTPHEADER => array(
			'ownerId: '.$setup['ownerId'],
			'requestUid: '.$setup['requestUid'],
			'timestamp: ' . time(),
			'authorization: Bearer '.$authorization,
			'Content-Type: application/json',
		  ),
		  CURLOPT_SSL_VERIFYHOST => 0, 
		  CURLOPT_SSL_VERIFYPEER => 0 
		));

		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			return ['error' => 'cURL error: ' . curl_error($curl)];
		}

		curl_close($curl);
		$responseData = json_decode($response, true);

		if (isset($responseData['data']) && is_array($responseData['data'])) {
			return $responseData['data'];
		} else {
			return [];
		}

	}

	function fnUnixTime($invdate){
		return strtotime($invdate.' 12:00:00');
	}
	 
?>