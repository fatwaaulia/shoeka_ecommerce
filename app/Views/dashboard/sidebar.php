<?php
$app_settings = model('AppSettings')->find(1);
$logo_web = webFile('image', 'app_settings', $app_settings['logo'], $app_settings['updated_at']);
?>

<header id="header" class="header fixed-top d-flex align-items-center" style="background-color: var(--main-color);">
	<div class="d-flex align-items-center justify-content-between">
		<a href="<?= base_url(userSession('slug_role')) . '/dashboard' ?>" class="logo d-flex align-items-center">
			<img src="<?= $logo_web ?>" alt="<?= $app_settings['nama_aplikasi'] ?>" title="<?= $app_settings['nama_aplikasi'] ?>">
		</a>
		<i class="fa-solid fa-bars toggle-sidebar-btn text-white"></i>
	</div>
	<nav class="header-nav ms-auto">
		<ul class="d-flex align-items-center">
            <div class="me-4 text-white">
				<label for="dark_mode">
					<i class="fa-solid fa-circle-half-stroke fa-lg" style="cursor: pointer;" title="Dark Light Mode"></i>
				</label>
				<div class="form-check form-switch d-none">
					<input class="form-check-input" type="checkbox" role="switch" id="dark_mode">
				</div>
			</div>
			<script>
			const darkmode_checkbox = dom('#dark_mode');
			const html = dom('#html');

			if (localStorage.getItem('is_dark_mode') === 'true') {
				html.setAttribute('data-bs-theme', 'dark');
				html.style.backgroundColor = '#1b1f22';
				darkmode_checkbox.checked = true;
			} else {
				html.setAttribute('data-bs-theme', 'light');
				html.removeAttribute('style');
				darkmode_checkbox.checked = false;
			}

			darkmode_checkbox.onclick = function() {
				if (darkmode_checkbox.checked) {
					localStorage.setItem('is_dark_mode', true);
					html.setAttribute('data-bs-theme', 'dark');
					html.style.backgroundColor = '#1b1f22';
					darkmode_checkbox.checked = true;
				} else {
					localStorage.removeItem('is_dark_mode');
					html.setAttribute('data-bs-theme', 'light');
					html.removeAttribute('style');
					darkmode_checkbox.checked = false;
				}
			}
			</script>
			<li class="nav-item dropdown">
				<a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
					<?php
					$nama = implode(' ', array_slice(explode(' ', userSession('nama')), 0, 3));
					$foto = webFile('image_user', 'users', userSession('foto'), userSession('updated_at'));
					?>
					<img src="<?= $foto ?>" alt="<?= userSession('nama') ?>" title="<?= userSession('nama') ?>" class="rounded-circle cover-center" style="width: 36px; height: 36px;">
					<span class="d-none d-md-block dropdown-toggle ps-2 text-white"><?= $nama ?></span>
				</a>
				<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
					<li class="text-center">
						<img src="<?= $foto ?>" alt="<?= userSession('nama') ?>" title="<?= userSession('nama') ?>" class="rounded-circle cover-center wh-100 mt-2">
						<div class="my-3">
							<h5><?= $nama ?></h5>
							<span><?= userSession('nama_role') ?></span>
						</div>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>
					<li>
						<a class="dropdown-item d-flex align-items-center" href="<?= base_url(userSession('slug_role')) . '/profile' ?>">
							<i class="fa-solid fa-user" style="font-size:16px"></i>
							<span>Profil</span>
						</a>
					</li>
					<li class="mb-2">
						<a class="dropdown-item d-flex align-items-center" href="<?= base_url('logout') ?>">
							<i class="fa-solid fa-arrow-right-from-bracket" style="font-size:16px"></i>
							<span>Keluar</span>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</nav>
</header>

<aside id="sidebar" class="sidebar">
	<ul class="sidebar-nav" id="sidebar-nav">
		<?php
		$menu = menuSidebar();

		$uri = service('uri');
        $uri->setSilent();
		$segment_1 = $uri->getSegment(1);
		$segment_2 = ($uri->getSegment(2) ? '/' . $uri->getSegment(2) : '');
		$segment_3 = ($uri->getSegment(3) ? '/' . $uri->getSegment(3) : '');
        $base_route	= base_url($segment_1 . $segment_2);

		// Custom Base Route
		if ($segment_2 == '/maintenance') {
			$base_route	= base_url($segment_1 . $segment_2 . $segment_3);
		}
		// End - Custom Base Route

		foreach ($menu as $v) :
			if (! isset($v['title'])) continue;
			if (in_array(userSession('id_role'), $v['role'])) : // Tampilkan Menu Sesuai Role
			if ($v['type'] == 'no-collapse') :
				$collapsed = ($base_route == $v['url']) ? '' : 'collapsed';
		?>
		<li class="nav-item">
			<a class="nav-link <?= $collapsed ?>" href="<?= $v['url'] ?>">
				<i class="<?= $v['icon'] ?>"></i>
				<span><?= $v['title'] ?></span>
			</a>
		</li>
			<?php
			elseif ($v['type'] == 'collapse') :
				$is_active = in_array($base_route, array_column($v['collapse'], 'url'));
                $collapsed = $is_active ? '' : 'collapsed';
                $collapse_show = $is_active ? 'show' : '';
			?>
		<li class="nav-item text-light">
			<a class="nav-link <?= $collapsed ?>" data-bs-target="#<?= url_title($v['title'], '-', true) ?>" data-bs-toggle="collapse" href="#">
				<i class="<?= $v['icon'] ?>"></i>
				<span><?= $v['title'] ?></span>
				<i class="fa-solid fa-angle-down ms-auto"></i>
			</a>
			<ul id="<?= url_title($v['title'], '-', true) ?>" class="nav-content collapse <?= $collapse_show ?>" data-bs-parent="#sidebar-nav">
				<?php foreach ($v['collapse'] as $collapse) : ?>
				<li>
					<a href="<?= $collapse['url'] ?>" class="<?= $collapse['url'] == $base_route ? 'active' : '' ?>">
						<i class="fa-solid fa-circle"></i>
						<span><?= $collapse['title'] ?></span>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
      	</li>
		<?php elseif (($v['type'] == 'heading')) : ?>
		<li class="nav-heading my-3">
			<i class="fa-solid fa-minus"></i>
			<?= $v['title'] ?>
		</li>
		<?php
			endif;
			endif;
		endforeach;
		?>
	</ul>
</aside>

<script>
// Saat Reload, Tetap Mempertahankan Posisi ScrollY Sidebar
document.addEventListener('DOMContentLoaded', () => {
    let sidebar = dom('#sidebar');
    if (sessionStorage.getItem('sidebar_scroll_position')) {
        sidebar.scrollTop = sessionStorage.getItem('sidebar_scroll_position');
    }
    sidebar.addEventListener('scroll', () => {
        sessionStorage.setItem('sidebar_scroll_position', sidebar.scrollTop);
    });
});
</script>

<script>
// Tombol Toggle Sidebar
(function() {
    "use strict";

    const select = (el, all = false) => all ? [...document.querySelectorAll(el.trim())] : document.querySelector(el.trim());
    const on = (type, el, listener, all = false) => {
        const elements = select(el, all);
        if (all && elements.length) {
            elements.forEach(elem => elem.addEventListener(type, listener));
        } else if (elements) {
            elements.addEventListener(type, listener);
        }
    };

    on('click', '.toggle-sidebar-btn', () => select('body').classList.toggle('toggle-sidebar'));
})();
</script>