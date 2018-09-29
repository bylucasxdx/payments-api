<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PaymentController extends Controller
{    
    public function transaction(Request $request) {
        // GARANTE QUE OS DADOS ESTÃO AQUI 
        // A REQUISIÇÃO RETORNA ERROR 422 EM CASO DE FALHA
        $this->validate($request, [
            'access_token' => 'required',
            'value_order' => 'required|numeric',
            'slices' => 'required|numeric',
            'card_name' => 'required',
            'card_number' => 'required',
            'card_expdate_month' => 'required',
            'card_expdate_year' => 'required',
            'card_cvv' => 'required'
        ]);

        // FAZ A REQUISIÇÃO PARA VERIFICAR O TOKEN DO USUÁRIO
        // $urlBaseAutentication = "http://localhost:3000/api/v1/public/authorization";

        // $client = new Client();
        // $res = $client->request('POST', $urlBaseAutentication, [
        //     'form_params' => [
        //         'access_token' => $request->input('access_token')
        //     ]
        // ]);
        // $res->getStatusCode();
        // $res->getHeader('content-type'));
        // $res->getBody();

        // GROUP THE CREDIT CARD DATA 
        $creditCard = $request->only(['card_name', 'card_number', 'card_expdate_month', 'card_expdate_year', 'card_cvv']);

        // VALIDATE THE CREDIT CARD DATA
        if ($creditCard['card_expdate_year'] < date('Y')) {
            return response()->json(array('card_expdate_year' => array('Year of expiration is invalid')), 422);
        }

        if ($creditCard['card_expdate_year'].'-'.$creditCard['card_expdate_month'] < date('Y-m')) {
            return response()->json(array('card_expdate_month' => array('Month of expiration is invalid')), 422);
        }

        // ENVIA PARA A API DE AUDITORIA
        $auditData = array(
            'source' => 'payment-api', 
            'creditCard' => $creditCard
        );

        // $urlBaseAudit = "http://localhost:3000/api/v1/public/audit";

        // $client = new Client();
        // $res = $client->request('POST', $urlBaseAudit, [
        //     'form_params' => $auditData
        // ]);
        // $res->getStatusCode();
        // $res->getHeader('content-type'));
        // $res->getBody();

        return response()->json(
            ['mensagem' => 'Pagamento concluido!']
        , 200);
    }
}
