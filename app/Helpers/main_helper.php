<?php

use Config\App;
use CodeIgniter\I18n\Time;

function encode($string)
{
    $key = "bismillah";
    return base64_encode(openssl_encrypt($string, 'aes-256-ecb', $key, 0));
}

function decode($string)
{
    $key = 'bismillah';
    return openssl_decrypt(base64_decode($string), 'aes-256-ecb', $key, 0);
}

function dateFormatter($tanggal, $format)
{   
    $date = Time::parse($tanggal, 'Asia/Jakarta', 'id_ID');
    return $date->toLocalizedString($format); // cccc, d MMMM yyyy HH:mm:ss
}

function userSession($field = null)
{
    if (! session()->isLogin) return '';

    $id_user = session()->get('id_user');
    $user = model('Users')->find($id_user);
    
    if (! $user) return 'Pengguna tidak ditemukan!';

    if ($field) {
        $user_session = $user[$field];
    } else {
        $user_session = $user;
    }

    return $user_session;
}

function dataTablesSearch($columns, $search, $select, $base_query) {
    $searchable = array_filter($columns);
    if ($search && $searchable) {
        $aliases = [];
        $has_alias = false;
        foreach ((array)$select as $s) {
            if (preg_match('/(.+)\s+as\s+(.+)/i', $s, $match)) {
                $aliases[trim($match[2])] = trim($match[1]);
                $has_alias = true;
            }
        }
        $base_query->groupStart();
        foreach ($searchable as $col) {
            $column_name = ($has_alias && !in_array($col, $aliases)) ? "a.{$col}" : $col;
            $base_query->orLike($column_name, $search);
        }
        $base_query->groupEnd();
    }
}

function dotsNumber($angka) {
    return number_format($angka, 0, ',', '.');
}

function formatRupiah($angka) {
    $angka = (int)$angka;
    $abs = number_format(abs($angka), 0, ',', '.');
    return ($angka < 0 ? '-Rp' : 'Rp') . $abs;
}

/*--------------------------------------------------------------
  # File Management
--------------------------------------------------------------*/
function dirUpload()
{
    return config(App::class)->dirUpload;
}

function webFile($type = '', $folder_name = '', $filename = '', $updated_at = null)
{
    $webfile_url = config(App::class)->webfileURL;
    $path = '';

    if ($type == 'image') {
        $path = $webfile_url . 'default.png';
    }

    if ($type == 'image_user') {
        $path = $webfile_url . 'user-default.png';
    }

    if ($filename) {
        $path = $webfile_url . "$folder_name/$filename?v=" . date('His', strtotime($updated_at));
    }

    return $path;
}

function compressConvertImage($get_file, $upload_path, $filename)
{
    $image = service('image');
    $image->withFile($get_file);
    $image->convert(IMAGETYPE_JPEG);
    $image->flatten(255, 255, 255);
    $image->save($upload_path . $filename, 60);
}

/*--------------------------------------------------------------
  # Menu Sidebar
--------------------------------------------------------------*/
function menuSidebar()
{
    $sub_kategori = model('SubKategori')->first();
    $url_konfig_produk = base_url() . 'koleksi?kategori=' . $sub_kategori['slug_kategori'] . '&sub=' . $sub_kategori['slug'] . '&config=produk';
    $menu_sidebar = [
		[
			'title'	=> 'Dashboard',
			'icon'	=> 'fa-solid fa-chart-line',
			'url'	=> base_url(userSession('slug_role')) . '/dashboard',
			'role'	=> [1, 3],
			'type'	=> 'no-collapse',
		],
		[
			'title'	=> 'Website',
			'icon'	=> 'fa-solid fa-house',
			'url'	=> '/',
			'role'	=> [1, 3],
			'type'	=> 'no-collapse',
		],
        [
            'title'	=> 'Pesanan',
            'icon'	=> 'fa-solid fa-receipt',
            'url'	=> base_url(userSession('slug_role')) . '/pesanan',
            'role'	=> [1, 3],
            'type'	=> 'no-collapse',
        ],
        [
            'title'	=> 'Voucher Belanja',
            'icon'	=> 'fa-solid fa-tags',
            'url'	=> base_url(userSession('slug_role')) . '/voucher-belanja',
            'role'	=> [1],
            'type'	=> 'no-collapse',
        ],
        [
            'title'	=> 'Potongan Ongkir',
            'icon'	=> 'fa-solid fa-scissors',
            'url'	=> base_url(userSession('slug_role')) . '/potongan-ongkir',
            'role'	=> [1],
            'type'	=> 'no-collapse',
        ],
		[
			'title'	=> 'MASTER DATA',
			'role'	=> [1],
			'type'	=> 'heading',
		],
        [
			'title'	=> 'Banner',
			'icon'	=> 'fa-solid fa-image',
			'url'	=> base_url() . userSession('slug_role') . '/banner',
			'role'	=> [1],
			'type'	=> 'no-collapse',
		],
		[
			'title'	=> 'Kategori',
			'icon'	=> 'fa-solid fa-table-cells-large',
			'url'	=> base_url(userSession('slug_role')) . '/kategori',
			'role'	=> [1],
			'type'	=> 'no-collapse',
		],
		[
			'title'	=> 'Sub Kategori',
			'icon'	=> 'fa-solid fa-table-cells-large',
			'url'	=> base_url(userSession('slug_role')) . '/sub-kategori',
			'role'	=> [1],
			'type'	=> 'no-collapse',
		],
		[
			'title'	=> 'Sub Sub Kategori',
			'icon'	=> 'fa-solid fa-table-cells-large',
			'url'	=> base_url(userSession('slug_role')) . '/sub-sub-kategori',
			'role'	=> [1],
			'type'	=> 'no-collapse',
		],
        [
            'title' => 'Konfigurasi Produk',
            'icon'  => 'fa-solid fa-gears',
            'url'   => $url_konfig_produk,
            'role'  => [1],
            'type'  => 'no-collapse',
        ],
		[
			'title'	=> 'ACCOUNT',
			'role'	=> [1, 3],
			'type'	=> 'heading',
		],
        [
			'title'	=> 'App Settings',
			'icon'	=> 'fa-solid fa-gear',
			'url'	=> base_url(userSession('slug_role')) . '/app-settings',
			'role'	=> [1],
			'type'	=> 'no-collapse',
		],
		[
			'title'	=> 'Profil',
			'icon'	=> 'fa-solid fa-user',
			'url'	=> base_url(userSession('slug_role')) . '/profile',
			'role'	=> [1],
			'type'	=> 'no-collapse',
		],
		[
			'title'	=> 'Keluar',
			'icon'	=> 'fa-solid fa-arrow-right-from-bracket',
			'url'	=> base_url('logout'),
			'role'	=> [1, 3],
			'type'	=> 'no-collapse',
		],
	];

    return $menu_sidebar;
}

function roleAccessByTitle($title) {
    $sidebar = menuSidebar();
    foreach ($sidebar as $item) {
		if (in_array(($item['type'] ?? ''), ['no-collapse', 'collapse'])) {
			if (($item['title'] ?? '') == $title) {
				return $item['role'];
			}
		}
    }
    return [];
}
