<?php

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Aws\Sns\Exception\InvalidSnsMessageException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('sns', function (Request $request) {
    // Instantiate the Message and Validator
    $message = Message::fromRawPostData();
    $validator = new MessageValidator();

    // Validate the message and log errors if invalid.
    try {
        $validator->validate($message);
    } catch (InvalidSnsMessageException $e) {
        Log::error('SNS Message Validation Error: ' . $e->getMessage());

        abort(422, 'SNS Message Validation Error: ' . $e->getMessage());
    }

    // Check the type of the message and handle the subscription.
    if ($message['Type'] === 'SubscriptionConfirmation') {
        // Confirm the subscription by sending a GET request to the SubscribeURL
        file_get_contents($message['SubscribeURL']);
    }

    if ($message['Type'] === 'Notification') {
        // Do whatever you want with the message body and data.
        echo $message['MessageId'] . ': ' . $message['Message'] . "\n";

        Log::info($message['MessageId'] . ': ' . $message['Message']);
     }

    
    // $message = Message::fromRawPostData();
    
    // // Validate the message
    // $validator = new MessageValidator();
    // $validator->validate($message);

    // $body = $request->getContent();
    
    // Log::info($body);
    
    // $messageType = $request->header('x-amz-sns-message-type');
    
    // $payload = json_decode($body);

    // switch ($messageType) {
    //     case 'SubscriptionConfirmation':
    //         $client = new Client();

    //         // $type = $request->get('Type');
    //         // $messageId = $request->get('MessageId');
    //         // $token = $request->get('Token');
    //         // $topicArn = $request->get('TopicArn');
    //         // $message = $request->get('Message');
    //         // $subscribeURL = $request->get('SubscribeURL');
    //         // $timestamp = $request->get('Timestamp');
    //         // $signatureVersion = $request->get('SignatureVersion');
    //         // $signature = $request->get('Signature');
    //         // $signingCertURL = $request->get('SigningCertURL');

    //         $response = $client->get($payload->SubscribeURL);
            
    //         Log::info($response);
            
    //         // dd($response);

    //         break;

    //     case 'Notification':
    //         # code...
    //         break;

    //     case 'UnsubscribeConfirmation':
    //         # code...
    //         break;
            
    //     default:
    //         # code...
    //         break;
    // }

    // return $request->all();
})->middleware(\Chelout\HttpLogger\Middlewares\HttpLogger::class);
