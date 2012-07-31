<?php

class Application_Model_Payment
{


static function process($inData){

	$cardNumber=$inData['cardData']['cardNumber']; //only approve for one dollar even, remember to void transaction
	$expMonth=$inData['cardData']['expMonth'];
	$expYear=$inData['cardData']['expYear'];
	$chargeTotal=$inData['cardData']['chargeTotal'];

	$cardNumber=preg_replace('/[^\S]/', '', $cardNumber);

	$ch =curl_init("https://ws.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl");
	$dir = __DIR__;
	$pemPath=$dir."/../../library/Credentials/FDGGWS_Certificate_WS1001178130._.1/WS1001178130._.1.pem";
	$keyPath=$dir."/../../library/Credentials/FDGGWS_Certificate_WS1001178130._.1/WS1001178130._.1.key";
	$sslPw="ckp_1343424713";
	$acctPassword="WS1001178130._.1:Gh8daJgG";

	$body = "
		<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
			<SOAP-ENV:Header />
			<SOAP-ENV:Body>
				<fdggwsapi:FDGGWSApiOrderRequest xmlns:fdggwsapi=\"http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi\">
					<v1:Transaction xmlns:v1=\"http://secure.linkpt.net/fdggwsapi/schemas_us/v1\">
						<v1:CreditCardTxType>
							<v1:Type>sale</v1:Type>
						</v1:CreditCardTxType>
						<v1:CreditCardData>
							<v1:CardNumber>$cardNumber</v1:CardNumber>
							<v1:ExpMonth>$expMonth</v1:ExpMonth>
							<v1:ExpYear>$expYear</v1:ExpYear>
						</v1:CreditCardData>
						<v1:Payment>
							<v1:ChargeTotal>$chargeTotal</v1:ChargeTotal>
						</v1:Payment>
					</v1:Transaction>
				</fdggwsapi:FDGGWSApiOrderRequest>
			</SOAP-ENV:Body>
		</SOAP-ENV:Envelope>
	";


	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSLCERT, $pemPath);
	curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
	curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $sslPw);

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $acctPassword);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);


	$xml=xml_parser_create('');
	$values=array();
	$index=array();
	xml_parse_into_struct($xml, $result, &$values, &$index);
	$outList=array();
	for ($i=0, $len=count($values); $i<$len; $i++){
		$outList[$values[$i]['tag']]=$values[$i]['value'];
	}

	return $outList;
}

}

