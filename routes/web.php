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

        Log::info('SNS message is valid');
    } catch (InvalidSnsMessageException $e) {
        Log::error('SNS Message Validation Error: ' . $e->getMessage());

        abort(422, 'SNS Message Validation Error: ' . $e->getMessage());
    }

    switch ($message['Type']) {
        case 'SubscriptionConfirmation':
            // Confirm the subscription by sending a GET request to the SubscribeURL
            file_get_contents($message['SubscribeURL']);
            
            break;

        case 'Notification':
            // Do whatever you want with the message body and data.
            // Log::info($message['MessageId'] . ': ' . $message['Message']);
            Log::info(
                json_encode($message)
            );
            
            break;

        case 'UnsubscribeConfirmation':
            // Unsubscribed in error? You can resubscribe by visiting the endpoint
            // provided as the message's SubscribeURL field.
            file_get_contents($message['SubscribeURL']);
            
            break;
    }
})->middleware(\Chelout\HttpLogger\Middlewares\HttpLogger::class);
