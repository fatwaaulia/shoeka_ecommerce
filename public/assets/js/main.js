document.addEventListener('input', (e) => {
    if (e.target.name === 'username') {
        e.target.value = e.target.value.toLowerCase().replace(/[^a-z0-9]/g, '');
    }
});

function runInlineScripts(element) {
    element.querySelectorAll("script").forEach(script => {
        const new_script = document.createElement("script");
        new_script.textContent = script.textContent;
        document.body.appendChild(new_script);
    });
}

function formatRupiah(angka) {
    angka = Number(angka);
    const abs = Math.abs(angka).toLocaleString('id-ID');
    return (angka < 0 ? '-Rp' : 'Rp') + abs;
}

function dotsNumber(str) {
    const angka = Number(String(str).replace(/\D/g, ''));
    return angka.toLocaleString('id-ID');
};

function numberOnly(str) {
    return parseInt(str.replace(/\D/g, ''));
}

async function unduhFile(el) {
    const tombol = el;
    const url = tombol.dataset.url;
    const filename = tombol.dataset.filename;
    let original_text = el.innerHTML;
    tombol.disabled = true;
    tombol.style.width = tombol.getBoundingClientRect().width + 'px';
    tombol.innerHTML = `<div class="spinner-border spinner-border-sm"></div>`;

    try {
        const response = await fetch(url);
        const blob = await response.blob();

        tombol.disabled = false;
        tombol.innerHTML = original_text;

        const link = Object.assign(document.createElement('a'), {
            href: URL.createObjectURL(blob),
            download: `${filename}`,
        });
        link.click();
        URL.revokeObjectURL(link.href);
    } catch (error) {
        console.error('Error:', error);
    }
}
