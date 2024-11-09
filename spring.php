<?php

class Spring {

    private $apiUrl = 'https://mtapi.net/?testMode=1';

    public function newPackage(array $order, array $params): array
    {
        // Construct request with required format
        $packageRequest = [
            'Apikey' => $params['api_key'],
            'Command' => 'OrderShipment',
            'Shipment' => [
                'LabelFormat' => $params['label_format'],
                'ShipperReference' => '',
                'DisplayId' => '123450000',
                'InvoiceNumber' => '678900000',
                'Service' => $params['service'],
                'Weight' => '0.85',
                'WeightUnit' => 'kg',
                'Length' => '20',
                'Width' => '10',
                'Height' => '10',
                'DimUnit' => 'cm',
                'Value' => '20',
                'Currency' => 'EUR',
                'CustomsDuty' => 'DDU',
                'Description' => 'CD',
                'DeclarationType' => 'SaleOfGoods',
                'DangerousGoods' => 'N',
                'ConsignorAddress' => [
                    'Name' => $order['sender_fullname'],
                    'Company' => $order['sender_company']
                ],
                'ConsigneeAddress' => [
                    'Name' => $order['delivery_fullname'],
                    'Company' => $order['delivery_company'],
                    'AddressLine1' => $order['delivery_address'],
                    'City' => $order['delivery_city'],
                    'State' => '', // Add if applicable
                    'Zip' => $order['delivery_postalcode'],
                    'Country' => $order['delivery_country'],
                    'Phone' => $order['delivery_phone'],
                    'Email' => $order['delivery_email'],
                    'Vat' => ''
                ],
                'Products' => [
                    [
                        'Description' => 'CD: The Postal Service - Give Up',
                        'Sku' => 'CD1202',
                        'HsCode' => '852349',
                        'OriginCountry' => 'GB',
                        'ImgUrl' => 'http://url.com/cd-thepostalservicegiveup.jpg',
                        'Quantity' => '2',
                        'Value' => '20',
                        'Weight' => '0.8'
                    ]
                ]
            ]
        ];

        $response = $this->makeRequest($packageRequest);

        if ($response['ErrorLevel'] !== 0) {
            echo 'Error creating package: ' . $response['Error'];
            exit;
        }

        return $response['Shipment'];
    }

    public function packagePDF(array $shipment)
    {
         // var_dump($shipment);

        if (isset($shipment['LabelImage'])) {
            // Decode base64-encoded PDF content and display
            header("Content-type: application/pdf");
            echo base64_decode($shipment['LabelImage']);
        } else {
            echo 'Error: Label image not found in response.';
            exit;
        }
    }

    private function makeRequest(array $data)
    {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Connection error: ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

        return json_decode($result, true);
    }

}