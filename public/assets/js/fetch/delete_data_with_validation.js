async function deleteDataWithValidation(form, endpoint) {
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
        })

        if (result.isConfirmed) {
            dom('#loading').innerHTML = `<div class="full-transparent"> <div class="spinner"> </div> </div>`;

            const form_data = new FormData(form);
            for (const [key, value] of form_data.entries()) {
                if (typeof value === 'string') {
                    form_data.set(key, value.trim());
                }
            }
            const response = await fetch(endpoint, {
                method: 'POST',
                body: form_data,
            });
            const data = await response.json();

            dom('#loading').innerHTML = ``;

            // Menangani semua elemen id="invalid_*" dan menampilkan pesan error secara dinamis
            Array.from(form.querySelectorAll('[id^="invalid_"]')).forEach(element => {
                const field = element.id.replace('invalid_', '');
                const element_by_name = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${field}[]"]`);
                element.textContent = data.errors?.[field] || '';
                if (element_by_name && !['radio', 'checkbox'].includes(element_by_name.type)) {
                    element_by_name.classList.toggle('is-invalid', !!data.errors?.[field]);
                    element_by_name.nextElementSibling?.querySelector('.dselect-wrapper > .form-select')?.classList.toggle('is-invalid', !!data.errors?.[field]);
                }
            });

            // console.log(data);
            // return;

            if (['success', 'error'].includes(data.status)) {
                await Swal.fire({
                    icon: data.status,
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
                data.route && (window.location.href = data.route);
            } else {
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
