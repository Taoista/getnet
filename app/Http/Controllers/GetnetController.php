<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\PerPage;
use Cookie;

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

        // * GUARDO LOS DATOS GENERADOS PARA CREAR EL ARRAY O EL OBJETO A ENVIAR 
        $pago = new PerPage;
        $pago->login = $this->login;
        $pago->tranKey = $tranKey;
        $pago->nonce = $nonceBase64;
        $pago->seed = $seed;
        $pago->save();

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
            "returnUrl"     => "http://127.0.0.1:8000/return_page/reference=".$pago->id,
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

        $data_response = $response->getBody();
        $data =  json_decode( $data_response, true);
        $requestId = $data["requestId"];

        // * ACTUALIZO EL requesID para saber que id unico de getnet segenera
        PerPage::where("id", $pago->id)->update(["requestId" => $requestId]);

        return $data_response;
    }


    function return_page(Request $request, $reference){

        $id = intval(str_replace("reference=", "", $reference));

        $data = PerPage::where("id", $id)->get()->first();
        // 6361/768c4c87303474071973b30f8be020e3

        $auth = array(
            "auth" => array(
                'login' => $data->login,
                'tranKey' => $data->tranKey,
                'nonce' => $data->nonce,
                'seed' => $data->seed
            ),
            "internalReference" => $data->requestId
        );


        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $response = $client->request('GET', 'https://checkout.uat.getnet.cl/api/session/'.$data->requestId,
            [
                'headers' => $headers,
                'json' => $auth
            ]
        );
        
        echo $response;
        
    }


    // * TESTING PRUEBA DE SOFTWARE
    public function demo_demo(){
        $pago = new PerPage;
        $pago->login = "demo_desde_postman";
        $pago->tranKey = "demo_desde_postman";
        $pago->nonce = "demo_desde_postman";
        $pago->seed = "demo_desde_postman";
        $pago->save();
        return "guardado correctamente 2";
    }


}
