<?php

namespace App\Controllers;

class Ongkir extends BaseController
{
    /*--------------------------------------------------------------
    # API
    --------------------------------------------------------------*/
    public function index()
    {
        if (empty(session('datetime'))) return;

        $tipe = $this->request->getVar('tipe');
        $kode = $this->request->getVar('kode');

        if ($tipe == 'provinsi') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://rajaongkir.komerce.id/api/v1/destination/province',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'key: 0kcuwsB2588e12617161919foW7jNt4W'
                ),
            ));
    
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
    
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $response['data'],
            ]);
        }

        if ($tipe == 'kabupaten') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://rajaongkir.komerce.id/api/v1/destination/city/' . $kode,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'key: 0kcuwsB2588e12617161919foW7jNt4W'
                ),
            ));
    
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
    
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $response['data'],
            ]);
        }

        if ($tipe == 'kecamatan') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://rajaongkir.komerce.id/api/v1/destination/district/' . $kode,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'key: 0kcuwsB2588e12617161919foW7jNt4W'
                ),
            ));
    
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
    
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $response['data'],
            ]);
        }

        if ($tipe == 'desa') {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://rajaongkir.komerce.id/api/v1/destination/sub-district/' . $kode,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'key: 0kcuwsB2588e12617161919foW7jNt4W'
                ),
            ));
    
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
    
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $response['data'],
            ]);
        }
    }

    public function tarif()
    {
        if (empty(session('datetime'))) return;

        $origin      = 46740; // Sawojajar, Kec. Kedungkandang, Malang.
        $destination = $this->request->getVar('destination');
        $weight      = $this->request->getVar('weight');
        $kurir       = $this->request->getVar('kurir');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost?origin=$origin&destination=$destination&weight=$weight&courier=$kurir",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'key: 0kcuwsB2588e12617161919foW7jNt4W'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        if (isset($response['data'])) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $response['data'],
            ]);
        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Layanan kurir tidak tersedia',
            ]);
        }
    }

    public function lacakResi()
    {
        if (empty(session('datetime'))) return;

        $awb   = $this->request->getVar('awb');
        $kurir = $this->request->getVar('kurir');
        $last_phone_number = $this->request->getVar('last_phone_number'); // required by JNE.

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/track/waybill?awb=$awb&courier=$kurir&last_phone_number=$last_phone_number",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'key: 0kcuwsB2588e12617161919foW7jNt4W',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        if (isset($response['data']['manifest'])) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $response['data']['manifest'],
            ]);
        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Resi tidak ditemukan',
            ]);
        }
    }
}
