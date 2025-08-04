<?php
$app_settings = model('AppSettings')->find(1);
$logo = webFile('image', 'app_settings', $app_settings['logo'], $app_settings['updated_at']);
?>

<style>
body { overflow: hidden; }

.bg-login {
    background-image: linear-gradient(rgba(0, 0, 0, .4), rgba(0, 0, 0, .4)), url('<?= base_url('assets/img/bg-login.jpg') ?>');
}
</style>

<section class="container-fluid bg-login cover-center">
	<div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-xxl-4 col-lg-4 col-md-6 col-12">
                <div class="card my-4 pt-3 pb-1" style="background-color: rgba(255, 255, 255, 0.7);">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="<?= $logo ?>" class="w-25 mb-2" alt="<?= $app_settings['nama_aplikasi'] ?>" title="<?= $app_settings['nama_aplikasi'] ?>" style="filter: brightness(0) saturate(100%) sepia(1) hue-rotate(170deg) saturate(600%) brightness(1.2);"> <br>
                            <h3 class="mb-1 fw-600">Login E-Commerce</h3>
                            <p>Silakan masuk ke akun Anda.</p>
                        </div>
                        <hr>
                        <form id="form">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="username">
                                <div class="invalid-feedback" id="invalid_username"></div>
                            </div>
                            <div class="mb-3">
                                <div class="position-relative">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="password" autocomplete="off">
                                    <div class="invalid-feedback" id="invalid_password"></div>
                                    <img src="<?= base_url('assets/icons/show.png') ?>" class="position-absolute" id="eye_password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Masuk</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleVisibility(inputElement, eyeElement) {
	const showIcon = "<?= base_url('assets/icons/show.png') ?>";
    const hideIcon = "<?= base_url('assets/icons/hide.png') ?>";
    inputElement.type = password.type === 'password' ? 'text' : 'password';
    eyeElement.src = password.type === 'password' ? showIcon : hideIcon;
}

const eyePassword = document.getElementById('eye_password');
const password = document.getElementById('password');
eyePassword.addEventListener('click', () => {
    toggleVisibility(password, eyePassword);
});

sessionStorage.removeItem("sidebarScrollPosition");
</script>

<script>
const form = document.getElementById('form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
	const endpoint = '<?= base_url() ?>api/login';
    submitData(form, endpoint);
});
</script>
