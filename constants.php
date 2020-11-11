<?php 

	/*Security*/
	define('SECRETE_KEY', 'test123');
	define('EMAIL_TAKEN', 						    98);
    define('NOT_OWN_POST', 						    99);
	/*Data Type*/
	define('BOOLEAN', 	'1');
	define('INTEGER', 	'2');
	define('STRING', 	'3');
	define('PASSWORD', 	'4');
	define('EMAIL', 	'5');

	/*Error Codes*/
	define('REQUEST_METHOD_NOT_VALID',		        100);
	define('REQUEST_CONTENTTYPE_NOT_VALID',	        101);
	define('REQUEST_NOT_VALID', 			        102);
    define('VALIDATE_PARAMETER_REQUIRED', 			103);
	define('VALIDATE_PARAMETER_DATATYPE', 			104);
	define('API_NAME_REQUIRED', 					105);
	define('API_PARAM_REQUIRED', 					106);
	define('API_DOST_NOT_EXIST', 					107);
	define('INVALID_USER_PASS', 					108);
	define('USER_NOT_ACTIVE', 						109);
	define('VALIDATION_ERROR', 					    110);

	define('SUCCESS_RESPONSE', 						200);

	/*Server Errors*/

	define('JWT_PROCESSING_ERROR',					300);
	define('ATHORIZATION_HEADER_NOT_FOUND',			301);
	define('ACCESS_TOKEN_ERRORS',					302);	
?>