document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('sa-download-invoice-pdf');
    var invoice = document.getElementById('sa-invoice-capture');

    if (!button || !invoice || typeof html2pdf === 'undefined') {
        return;
    }

    button.addEventListener('click', function () {
        var originalText = button.textContent;
        var filename = button.getAttribute('data-filename') || 'facture.pdf';

        button.disabled = true;
        button.textContent = 'Génération du PDF...';

        var clone = invoice.cloneNode(true);
        clone.id = 'sa-invoice-capture-pdf-temp';

        clone.style.width = '100%';
        clone.style.maxWidth = '100%';
        clone.style.margin = '0';
        clone.style.padding = '35px';
        clone.style.boxSizing = 'border-box';
        clone.style.background = '#ffffff';

        var wrapper = document.createElement('div');
        wrapper.style.position = 'fixed';
        wrapper.style.left = '-99999px';
        wrapper.style.top = '0';
        wrapper.style.width = '210mm';
        wrapper.style.background = '#ffffff';
        wrapper.style.padding = '0';
        wrapper.style.margin = '0';
        wrapper.style.boxSizing = 'border-box';
        wrapper.appendChild(clone);

        document.body.appendChild(wrapper);

        var opt = {
            margin: [0, 0, 0, 0],
            filename: filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['css', 'legacy']
            }
        };

        html2pdf()
            .set(opt)
            .from(clone)
            .save()
            .then(function () {
                document.body.removeChild(wrapper);
                button.disabled = false;
                button.textContent = originalText;
            })
            .catch(function (error) {
                console.error(error);
                if (wrapper.parentNode) {
                    document.body.removeChild(wrapper);
                }
                button.disabled = false;
                button.textContent = originalText;
                alert('Impossible de générer le PDF.');
            });
    });
});