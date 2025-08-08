async function submitDataWithConfirm(form, endpoint, confirm_title = '', confirm_message = '', confirm_button_text = 'Iya') {
    const tombol_submit = event.submitter;
    let original_text = tombol_submit.innerHTML;

    try {
        const result = await Swal.fire({
            icon: 'question',
            title: confirm_title,
            text: confirm_message,
            confirmButtonText: confirm_button_text,
            cancelButtonText: 'Batal',
            showCancelButton: true,
            reverseButtons: true,
        })

        if (result.isConfirmed) {
            tombol_submit.disabled = true;
            tombol_submit.style.width = tombol_submit.getBoundingClientRect().width + 'px';
            tombol_submit.innerHTML = `<div class="spinner-border spinner-border-sm"></div>`;
        
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

            tombol_submit.disabled = false;
            tombol_submit.innerHTML = original_text;

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

            console.log(data);
            return;

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
        tombol_submit.disabled = false;
        tombol_submit.innerHTML = original_text;

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
