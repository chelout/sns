<?php

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
    $messageType = $request->header('x-amz-sns-message-type');

    switch ($messageType) {
        case 'SubscriptionConfirmation':
            $client = new Client([
                // You can set any number of default request options.
                'timeout'  => 2.0,
            ]);

            $type = $request->get('Type');
            $messageId = $request->get('MessageId');
            $token = $request->get('Token');
            $topicArn = $request->get('TopicArn');
            $message = $request->get('Message');
            $subscribeURL = $request->get('SubscribeURL');
            $timestamp = $request->get('Timestamp');
            $signatureVersion = $request->get('SignatureVersion');
            $signature = $request->get('Signature');
            $signingCertURL = $request->get('SigningCertURL');

            $response = $client->get($subscribeURL);
            
            Log::info($response);
            
            // dd($response);

            break;

        case 'Notification':
            # code...
            break;

        case 'UnsubscribeConfirmation':
            # code...
            break;
            
        default:
            # code...
            break;
    }

    // return $request->all();
})->middleware(\Chelout\HttpLogger\Middlewares\HttpLogger::class);
