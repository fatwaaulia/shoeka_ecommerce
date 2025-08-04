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
$routes->get('koleksi', 'FrontEnd::koleksi');

// Detail Produk
$routes->get('detail-produk/(:segment)', 'FrontEnd::detailProduk/$1');

// Keranjang
$routes->get('keranjang', 'FrontEnd::keranjang');
$routes->post('keranjang', 'FrontEnd::keranjangSession');
$routes->post('api/invoice/create', 'FrontEnd::createInvoice');

// Transaksi
$routes->get('detail-transaksi', 'FrontEnd::detailTransaksi');
$routes->post('webhook/xendit', 'Webhook::xendit');

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

if (in_array($id_role, [1, 2])) {
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

if (in_array($id_role, roleAccessByTitle('Galeri'))) {
    $routes->group("$slug_role/galeri", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Galeri::main');
        $routes->get('new', 'Galeri::new');
        $routes->get('edit/(:segment)', 'Galeri::edit/$1');
    });
    $routes->group('api/galeri', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Galeri::index');
        $routes->post('create', 'Galeri::create');
        $routes->post('update/(:segment)', 'Galeri::update/$1');
        $routes->post('delete/(:segment)', 'Galeri::delete/$1');
    });
}

/*--------------------------------------------------------------
  # Master Data
--------------------------------------------------------------*/
if (in_array($id_role, roleAccessByTitle('Role'))) {
    $routes->get("$slug_role/role", 'Role::main', ['filter' => 'EnsureLogin']);
    $routes->group('api/role', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Role::index');
        $routes->post('create', 'Role::create');
        $routes->post('update/(:segment)', 'Role::update/$1');
        $routes->post('delete/(:segment)', 'Role::delete/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('User Management'))) {
    $routes->group("$slug_role/users", ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Users::main');
        $routes->get('new', 'Users::new');
        $routes->get('edit/(:segment)', 'Users::edit/$1');
    });
    $routes->group('api/users', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->post('create', 'Users::create');
        $routes->post('update/(:segment)', 'Users::update/$1');
        $routes->post('delete/(:segment)', 'Users::delete/$1');
        $routes->post('foto/delete/(:segment)', 'Users::hapusFoto/$1');
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
        $routes->post('update/(:segment)/json-id-varian-produk', 'SubKategori::updateJsonIdVarianProduk/$1');
        $routes->post('delete/(:segment)', 'SubKategori::delete/$1');
    });
}

if (in_array($id_role, roleAccessByTitle('Sub Sub Kategori'))) {
    $routes->get("$slug_role/sub-sub-kategori", 'SubSubKategori::main', ['filter' => 'EnsureLogin']);
    $routes->group('api/sub-sub-kategori', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'SubSubKategori::index');
        $routes->post('create', 'SubSubKategori::create');
        $routes->post('update/(:segment)', 'SubSubKategori::update/$1');
        $routes->post('update/(:segment)/json-id-varian-produk', 'SubKategori::updateJsonIdVarianProduk/$1');
        $routes->post('delete/(:segment)', 'SubSubKategori::delete/$1');
    });
}

if (userSession()) {
    $routes->group('api/varian-produk', ['filter' => 'EnsureLogin'], static function ($routes) {
        $routes->get('/', 'VarianProduk::index');
    });
}
