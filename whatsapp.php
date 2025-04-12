<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://graph.facebook.com/v15.0/107700822139491/messages',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "messaging_product": "whatsapp",
   "to": "573108635298",
   "type": "template",
   "template": {
       "name": "bienvenida_gsgroup",
       "language": {
           "code": "es_MX",
           "policy": "deterministic"
       },
       "components": [
           {
               "type": "body",
               "parameters": [
                   {
                       "type": "text",
                       "text": "*Ginna Daza*"
                   },
                   {
                       "type": "text",
                       "text": "Sebastián Guzmán"
                   },
                   {
                       "type": "text",
                       "text": "http://gsgroup.rf.gd"
                   },
                   {
                       "type": "text",
                       "text": "3012730678"
                   },
                   {
                       "type": "text",
                       "text": "123A"
                   }
               ]
           } 
       ]
   }
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer EAAJYv68NTfoBAHxZAL0YW8OHBvrgTRFZAiy62U6z8i1ZBy7n3zNRmCOqiapdH42jAf3WW0EXDwGUqidxDOrKE27JNDrWoftSMAmkXxxbhSpyZC8kqZCQTAIIwvrUTcqKe0nIj6lwEzMaiBAS65mB35lsqRS4GjQINz6lBwTg5PZCNf9Rp82dRC29aIb0ZAetW3W84BVaRqGZCZBpe73JJENZAp',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>