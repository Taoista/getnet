<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;


class GetnetController extends Controller{
    
    public $login;
    public $secretKey;

    function __construct(){
        $debug = config('app.debug');

        if($debug == true){
            $this->login = '7ffbb7bf1f7361b1200b2e8d74e1d76f';
            $this->secretKey = "SnZP3D63n3I9dH9O";
        }else{
            $this->login = env("GETNET_LOGIN");
        }
        
    }

    function index(){
        return view("index");
    }


    public function generate_token(){
        header('Content-Type: application/json');

        $seed = date('c');

        if(function_exists('random_bytes')) {
            $nonce = bin2hex(random_bytes(16));
        }elseif(function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        }else{
            $nonce = mt_rand();
        }

        $nonceBase64 = base64_encode($nonce);
        $tranKey = base64_encode(hash('sha1', $nonce . $seed . $this->secretKey, true));
        $expiration = date('c', strtotime('+20 minutes', strtotime($seed)));

        $auth = array(
            "auth" => array(
                'login' => $this->login,
                'tranKey' => $tranKey,
                'nonce' => $nonceBase64,
                'seed' => $seed
            ),
            "locate" => "es_CL",
            "buyer" => array(
                "name" => "luis",
                "surname" => "Olave",
                "email" => "luis.olave.carvajal@gmail.com",
                "document" => "16803933-6",
                "mobile" => "968300554"
            ),
            "payment"=> array(
                "reference" => "123123",
                "description" => "Prueba 17 de Agosto",
                "amount" => array(
                    "currency" => "CLP",
                    "total" => "200"
                ),
                "allowPartial" => false
            ),
            "expiration"    => $expiration,
            // "returnUrl"     => "http://127.0.0.1:8000/return_page/?login=".$this->login."&tranKey=".$tranKey."&nonce=".$nonceBase64."&seed=".$seed,
            "returnUrl"     => "http://127.0.0.1:8000/return_page/",
            "ipAddress"     => "127.0.0.1",
            "userAgent"     => "neumachile",
            "skipResult"    => false
        );
        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $response = $client->request('POST', 'https://checkout.test.getnet.cl/api/session/',
            [
                'headers' => $headers,
                'json' => $auth
            ]
        );

        return $response->getBody();
    }


    function return_page(Request $request){
        // $seed = date('c');
        // $auth = array(
        //     "auth" => array(
        //         'login' => $request->login,
        //         'tranKey' => $request->tranKey,
        //         'nonce' => $request->nonceBase64,
        //         'seed' => $seed
        //     ),
        // );

        // $client = new Client();
        // $headers = [
        //     'Accept' => 'application/json',
        //     'Content-Type' => 'application/json',
        // ];
        // $response = $client->request('GET', 'https://checkout.test.getnet.cl/getRequestInformation/',
        //     [
        //         'headers' => $headers,
        //         'json' => $auth
        //     ]
        // );
            echo $request;
            // dd($request);
        // return $response->getBody();
    }

}
