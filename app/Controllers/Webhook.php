<?php

namespace App\Controllers;

class Webhook extends BaseController
{
    public function xendit()
    {
        $json     = file_get_contents('php://input');
        $response = json_decode($json, true);

        $data = [
            'input'      => json_encode($response, true),
            'invoice_id' => $response['id'] ?? '',
            'kode'       => $response['external_id'] ?? '',
        ];
        model('Webhook')->insert($data);
        
        if (isset($response['id'])) {
            $transaksi = model('Transaksi')->where('invoice_id', $response['id'])->first();

            if ($transaksi) {
                $api_key = 'xnd_development_Z745AIUbLnrvgz9JtyGSV8mF1UNarORVsj62mirDsKFHCDtsxrzgA9rcueAR9nd';
                $api_key_base64 = base64_encode($api_key);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.xendit.co/v2/invoices/' . $response['id'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS => '',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Basic ' . $api_key_base64,
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);

                $response = json_decode($response, true);

                $status = 'Menunggu Pembayaran';
                if ($response['status'] == 'PENDING') {
                    $status = 'Menunggu Pembayaran';
                } elseif ($response['status'] == 'PAID') {
                    $status = 'Lunas';
                } elseif ($response['status'] == 'SETTLED') {
                    $status = 'Lunas';
                } elseif ($response['status'] == 'EXPIRED') {
                    $status = 'Kedaluwarsa';
                }

                $data = [
                    'status'         => $status,
                    'invoice_status' => $response['status'],
                    'paid_at'        => $response['paid_at'] ?? null,
                ];
                model('Transaksi')->update($transaksi['id'], $data);

                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => 'success',
                    'message' => 'Webhook xendit berhasil',
                    'data'    => $response,
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Transaksi tidak ditemukan!',
                    'data'    => $response,
                ]);
            }
        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Webhook xendit gagal',
                'data'    => $response,
            ]);
        }
    }
}
