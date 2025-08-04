<?php

namespace App\Controllers;

class Maintenance extends BaseController
{
    protected $base_name;

    public function __construct()
    {
        $this->base_name = 'maintenance';
    }

    /*--------------------------------------------------------------
    # Email
    --------------------------------------------------------------*/
    public function email()
    {
        $data = [
            'base_api' => $this->base_api,
            'data'     => model('AppSettings')->find(1),
            'title'    => 'Maintenance Email',
        ];
        
        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view($this->base_name . '/email', $data);
        return view('dashboard/header', $view);
    }

    public function emailLayout()
    {
        $data = [
            'for_name'    => 'Hamba Allah',
            'message'     => 'Ada pesan nih, baca ya: <br>' .
                             'Dan Dia telah memberikan kepadamu segala apa yang kamu mohonkan kepada-Nya. Dan jika kamu menghitung nikmat Allah, niscaya kamu tidak akan mampu menghitungnya. Sungguh, manusia itu sangat zalim dan sangat mengingkari (nikmat Allah). (QS. Ibrahim : 34)',
            'button_link' => base_url(),
            'button_name' => 'Tombol',
        ];

        return view($this->base_name . '/email_layout', $data);
    }

    public function sendEmail()
    {
        $rules = [
            'email' => 'required|valid_email',
        ];
        if (! $this->validate($rules)) {
            $errors = array_map(fn($error) => str_replace('_', ' ', $error), $this->validator->getErrors());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors'  => $errors,
            ]);
        }

        $email  = $this->request->getVar('email');

        $to_email = $email;
        $subject = 'Kirim Email Berhasil';
        $message_field = [
           'for_name'     => 'Hamba Allah',
            'message'     => 'Ada pesan nih, baca ya: <br>' .
                             'Dan Dia telah memberikan kepadamu segala apa yang kamu mohonkan kepada-Nya. Dan jika kamu menghitung nikmat Allah, niscaya kamu tidak akan mampu menghitungnya. Sungguh, manusia itu sangat zalim dan sangat mengingkari (nikmat Allah). (QS. Ibrahim : 34)',
            'button_link' => base_url(),
            'button_name' => 'Tombol',
        ];
        $message = view($this->base_name . '/email_layout', $message_field);

        $email = service('email');
        $email->setFrom($email->fromEmail, $email->fromName);
        $email->setTo($to_email);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Permintaan telah dikirim. Silakan periksa email Anda!',
            ]);
        } else {
            $email_error = $email->printDebugger(['headers']);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Permintaan gagal diproses',
                'data'    => $email_error,
            ]);
        }
    }
}
