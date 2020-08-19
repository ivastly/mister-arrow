<?php

declare(strict_types=1);

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Enumeration\ContainmentModeType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\Type\AndType;
use jamesiarmes\PhpEws\Type\ConstantValueType;
use jamesiarmes\PhpEws\Type\ContainsExpressionType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
use jamesiarmes\PhpEws\Type\IsGreaterThanOrEqualToType;
use jamesiarmes\PhpEws\Type\IsLessThanOrEqualToType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\RestrictionType;

require_once 'vendor/autoload.php';

$config = require __DIR__ . '/config/config.php';
$client = new Client(
	$config['ms_exchange_host'],
	$config['email'],
	$config['exchange_password'],
	$config['ms_exchange_version']
);

$start_date = new DateTime('-1 minute');
$end_date   = new DateTime('now');
$timezone   = 'Eastern Standard Time';

$client->setTimezone($timezone);
$request                  = new FindItemType();
$request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

$greater_than                                      = new IsGreaterThanOrEqualToType();
$greater_than->FieldURI                            = new PathToUnindexedFieldType();
$greater_than->FieldURI->FieldURI                  = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
$greater_than->FieldURIOrConstant                  = new FieldURIOrConstantType();
$greater_than->FieldURIOrConstant->Constant        = new ConstantValueType();
$greater_than->FieldURIOrConstant->Constant->Value = $start_date->format('c');

$less_than                                      = new IsLessThanOrEqualToType();
$less_than->FieldURI                            = new PathToUnindexedFieldType();
$less_than->FieldURI->FieldURI                  = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
$less_than->FieldURIOrConstant                  = new FieldURIOrConstantType();
$less_than->FieldURIOrConstant->Constant        = new ConstantValueType();
$less_than->FieldURIOrConstant->Constant->Value = $end_date->format('c');

$request->Restriction                              = new RestrictionType();
$request->Restriction->And                         = new AndType();
$request->Restriction->And->IsGreaterThanOrEqualTo = $greater_than;
$request->Restriction->And->IsLessThanOrEqualTo    = $less_than;

$containsExpression                  = new ContainsExpressionType();
$containsExpression->ContainmentMode = new ContainmentModeType();

// It says this is deprecated, but what is the correct way to do it?
$containsExpression->ContainmentMode->_ = ContainmentModeType::SUBSTRING;
$containsExpression->Constant           = new ConstantValueType();
$containsExpression->Constant->Value    = 'hotline@doclerholding.com';
$containsExpression->FieldURI           = new PathToUnindexedFieldType();
$containsExpression->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DISPLAY_TO;
$request->Restriction->And->Contains    = $containsExpression;

$request->ItemShape                                = new ItemResponseShapeType();
$request->ItemShape->BaseShape                     = DefaultShapeNamesType::ALL_PROPERTIES;
$folder_id                                         = new DistinguishedFolderIdType();
$folder_id->Id                                     = DistinguishedFolderIdNameType::INBOX;
$request->ParentFolderIds->DistinguishedFolderId[] = $folder_id;

$response = $client->FindItem($request);

$response_messages = $response->ResponseMessages->FindItemResponseMessage;
foreach ($response_messages as $response_message)
{
	if ($response_message->ResponseClass !== ResponseClassType::SUCCESS)
	{
		$code    = $response_message->ResponseCode;
		$message = $response_message->MessageText;
		fwrite(
			STDERR,
			"Failed to search for messages with $code: $message\n"
		);
		exit;
	}

	$items = $response_message->RootFolder->Items->Message;

	foreach ($items as $item)
	{
		if (preg_match('/(mailer)|(message)|(messenger)|(jasmincore)/i', $item->Subject))
		{
			echo 'hotline';
			exit;
		}
	}
}

echo 'nothing';
