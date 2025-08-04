async function deleteData(endpoint, load_function = false) {
    try {
        const result = await Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Hapus',
            text: 'Setelah dihapus, tidak dapat dikembalikan!',
            confirmButtonText: 'Iya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
        });

        if (result.isConfirmed) {
            dom('#loading').innerHTML = `<div class="full-transparent"> <div class="spinner"> </div> </div>`;

            const response = await fetch(endpoint, {
                method: 'POST',
            });
            const data = await response.json();

            dom('#loading').innerHTML = ``;

            // console.log(data);
            // return;

            if (data.status == 'success') {
                if (load_function) {
                    window[load_function]();
                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
                window.location.reload();
            }  else {
                await Swal.fire({
                    icon: 'error',
                    title: data.message,
                    showConfirmButton: false,
                });
            }
        }
    } catch (error) {
        dom('#loading').innerHTML = ``;

        console.error(error);
        await Swal.fire({
            icon: 'error',
            title: 'Oops! Terjadi kesalahan',
            text: 'Silakan coba lagi nanti.',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    }
}
