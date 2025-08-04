<section class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="my-4"><?= isset($title) ? $title : '' ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table>
                        <tr>
                            <td>Kirim Email</td>
                            <td>: <a href="#" data-bs-toggle="modal" data-bs-target="#kirimEmail">Periksa</a></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="kirimEmail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Kirim Email</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email yang dituju</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@gmail.com">
                        <div class="invalid-feedback" id="invalid_email"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
dom('#form').addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>email';
    submitData(dom('#form'), endpoint);
});
</script>
