<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->set404Override(
    function() {
        $data['title'] = '404';
        $data['content'] = view('errors/error_404');

        if (userSession()) {
            $data['sidebar'] = view('dashboard/sidebar');
            return view('dashboard/header', $data);
        } else {
            $data['navbar'] = view('frontend/components/navbar');
            $data['footer'] = view('frontend/components/footer');
            return view('frontend/header', $data);
        }
    }
);

/*--------------------------------------------------------------
  # Front-End
--------------------------------------------------------------*/
$routes->get('/', 'FrontEnd::beranda');
$routes->get('pencarian', 'FrontEnd::pencarian');
$routes->get('koleksi', 'FrontEnd::koleksi');
$routes->get('tentang', 'FrontEnd::tentang');
$routes->get('faq', 'FrontEnd::faq');

// Detail Produk
$routes->get('detail-produk/(:segment)', 'FrontEnd::detailProduk/$1');

// Keranjang
$routes->get('keranjang', 'FrontEnd::keranjang');

// Checkout
$routes->get('checkout', 'FrontEnd::checkout');
$routes->get('api/voucher-belanja/kode/(:segment)', 'VoucherBelanja::cekKode/$1');
$routes->post('api/pesanan/create', 'Pesanan::create');

// Keranjang Session
$routes->post('session/keranjang/create', 'KeranjangSession::create');
$routes->post('session/keranjang/update/(:segment)', 'KeranjangSession::update/$1');
$routes->post('session/keranjang/delete/(:segment)', 'KeranjangSession::delete/$1');

// Raja Ongkir
$routes->get('api/ongkir/wilayah', 'Ongkir::index');
$routes->post('api/ongkir/tarif', 'Ongkir::tarif');
$routes->get('api/ongkir/resi', 'Ongkir::lacakResi');

// Pesanan
$routes->get('detail-pesanan', 'FrontEnd::detailPesanan');
$routes->post('webhook/doku', 'Webhook::doku');
$routes->get('api/pesanan/detail/(:segment)', 'Pesanan::detail/$1');

/*--------------------------------------------------------------
  # Autentikasi
--------------------------------------------------------------*/
// login
$routes->get('login', 'Auth::login');
$routes->post('api/login', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');

/*--------------------------------------------------------------
  # Menu Dashboard dan Profil
--------------------------------------------------------------*/
$id_role   = userSession('id_role');
$slug_role = userSession('slug_role');

if (in_array($id_role, [1, 3])) {
    $routes->get("$slug_role/dashboard", "Dashboard::$slug_role", ['filter' => 'EnsureLogin']);
    $routes->get("$slug_role/profile", "Profile::profilev1", ['filter' => 'EnsureLogin']);
    $routes->group("api/profile", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->post('update', 'Profile::updateProfilev1');
        $routes->post('update/password', 'Profile::updatePassword');
        $routes->post('delete/photo', 'Profile::deletePhoto');
    });
}

if ($id_role == 3) {
    $routes->get("$slug_role/dashboard", "Dashboard::$slug_role", ['filter' => 'EnsureLogin']);
    $routes->get("$slug_role/profile", "Profile::profilev2", ['filter' => 'EnsureLogin']);
    $routes->group('api/profile', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->post('update', 'Profile::updateProfilev2');
        $routes->post('update/password', 'Profile::updatePassword');
        $routes->post('delete/photo', 'Profile::deletePhoto');
    });
}

/*--------------------------------------------------------------
  # Menu Sidebar
--------------------------------------------------------------*/
if (userSession('id_role') == 1) {
    $routes->group("$slug_role/app-settings", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'AppSettings::edit');
    });
    $routes->group("api/app-settings", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->post('update/(:segment)', 'AppSettings::update/$1');
    });
}

if (userSession('id_role') == 1) {
    $routes->group("$slug_role/maintenance", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('email', 'Maintenance::email');
    });
    $routes->group("api/maintenance", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->post('email', 'Maintenance::sendEmail');
    });
}

if (userSession('id_role') == 1) {
    $routes->get("$slug_role/log-login", 'LogLogin::main', ['filter' => 'EnsureLogin']);
    $routes->group('api/log-login', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'LogLogin::index');
        $routes->post('delete/(:segment)', 'LogLogin::delete/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('Pesanan'))) {
    $routes->group("$slug_role/pesanan", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Pesanan::main');
        $routes->get('struk-alamat', 'Pesanan::strukAlamat');
    });
    $routes->group('api/pesanan', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Pesanan::index');
        $routes->post('update/(:segment)/nomor-resi', 'Pesanan::updateNomorResi/$1');
        $routes->post('update/(:segment)/status', 'Pesanan::updateStatus/$1');
        $routes->post('delete/(:segment)', 'Pesanan::delete/$1');
        $routes->get('sinkronisasi/(:segment)', 'Pesanan::sinkronisasi/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('Voucher Belanja'))) {
    $routes->group("$slug_role/voucher-belanja", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'VoucherBelanja::main');
        $routes->get('new', 'VoucherBelanja::new');
        $routes->get('edit/(:segment)', 'VoucherBelanja::edit/$1');
    });
    $routes->group('api/voucher-belanja', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'VoucherBelanja::index');
        $routes->post('create', 'VoucherBelanja::create');
        $routes->post('update/(:segment)', 'VoucherBelanja::update/$1');
        $routes->post('delete/(:segment)', 'VoucherBelanja::delete/$1');
    });
}

$routes->get('api/metode-pembayaran', 'MetodePembayaran::index');
$routes->get('api/potongan-ongkir/aktif', 'PotonganOngkir::aktif');
if (in_array($id_role, roleAccessByTitle('Potongan Ongkir'))) {
    $routes->group("$slug_role/potongan-ongkir", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'PotonganOngkir::main');
        $routes->get('new', 'PotonganOngkir::new');
        $routes->get('edit/(:segment)', 'PotonganOngkir::edit/$1');
    });
    $routes->group('api/potongan-ongkir', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'PotonganOngkir::index');
        $routes->post('create', 'PotonganOngkir::create');
        $routes->post('update/(:segment)', 'PotonganOngkir::update/$1');
        $routes->post('delete/(:segment)', 'PotonganOngkir::delete/$1');
    });
}

/*--------------------------------------------------------------
  # Master Data
--------------------------------------------------------------*/
if (in_array($id_role, roleAccessByTitle('Banner'))) {
    $routes->group("$slug_role/banner", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Banner::main');
        $routes->get('new', 'Banner::new');
        $routes->get('edit/(:segment)', 'Banner::edit/$1');
    });
    $routes->group('api/banner', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Banner::index');
        $routes->post('create', 'Banner::create');
        $routes->post('update/(:segment)', 'Banner::update/$1');
        $routes->post('delete/(:segment)', 'Banner::delete/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('Kategori'))) {
    $routes->get("$slug_role/kategori", 'Kategori::main', ['filter' => 'EnsureLogin']);
    $routes->group('api/kategori', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Kategori::index');
        $routes->post('create', 'Kategori::create');
        $routes->post('update/(:segment)', 'Kategori::update/$1');
        $routes->post('delete/(:segment)', 'Kategori::delete/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('Sub Kategori'))) {
    $routes->get("$slug_role/sub-kategori", 'SubKategori::main', ['filter' => 'EnsureLogin']);
    $routes->group('api/sub-kategori', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'SubKategori::index');
        $routes->post('create', 'SubKategori::create');
        $routes->post('update/(:segment)', 'SubKategori::update/$1');
        $routes->post('update/(:segment)/json-id-produk', 'SubKategori::updateJsonIdProduk/$1');
        $routes->post('delete/(:segment)', 'SubKategori::delete/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('Sub Sub Kategori'))) {
    $routes->get("$slug_role/sub-sub-kategori", 'SubSubKategori::main', ['filter' => 'EnsureLogin']);
    $routes->group('api/sub-sub-kategori', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'SubSubKategori::index');
        $routes->post('create', 'SubSubKategori::create');
        $routes->post('update/(:segment)', 'SubSubKategori::update/$1');
        $routes->post('update/(:segment)/json-id-produk', 'SubSubKategori::updateJsonIdProduk/$1');
        $routes->post('delete/(:segment)', 'SubSubKategori::delete/$1');
    });
}

if (userSession()) {
    $routes->group('api/produk', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Produk::index');
    });

    $routes->group('api/varian-produk', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'VarianProduk::index');
    });
}

$routes->get('api/varian-produk/(:segment)', 'VarianProduk::detail/$1');
