<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function superadmin()
    {
        $data['title'] = 'Dashboard';

        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view('dashboard/superadmin', $data);
        return view('dashboard/header', $view);
    }

    public function kasir()
    {
        $data['title'] = 'Dashboard';

        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view('dashboard/superadmin', $data);
        return view('dashboard/header', $view);
    }
}
