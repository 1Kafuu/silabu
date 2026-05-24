$(document).ready(function () {
    // ── Icon animation ─────────────────────────────────────────
    const $scanBtn = $('#btn-scan-nfc');
    const $wrapper = $('#nfc-icon-wrapper');
    const $icon    = $wrapper.find('.mdi');

    const observer = new MutationObserver(function () {
        if ($scanBtn.hasClass('btn-warning')) {
            $wrapper.addClass('scanning').removeClass('success error');
            $icon.removeClass('text-success text-danger').addClass('text-primary');
        }
    });
    observer.observe($scanBtn[0], { attributes: true, attributeFilter: ['class'] });

    // ── Submit attendance ──────────────────────────────────────
    $('#btn-submit').on('click', function () {
        const serial = $('#serial_number').val().trim();
        if (!serial) return;

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

        $.ajax({
            url    : window.AttendanceConfig.scanUrl,
            method : 'POST',
            data   : {
                _token        : window.AttendanceConfig.csrfToken,
                serial_number : serial,
            },
            success: function (res) {
                showResult('success', res.message, res.student, res.nim, res.scan_time);
                appendRow(res.student, res.nim, res.scan_time);
                resetForm();
            },
            error: function (xhr) {
                const res = xhr.responseJSON || {};
                const msg = res.message || 'Terjadi kesalahan. Coba lagi.';
                showResult('danger', msg, res.student || null, null, null);
                resetForm();
            },
        });
    });

    // ── Helpers ────────────────────────────────────────────────
    function showResult(type, message, studentName, nim, scanTime) {
        const iconMap = {
            success : 'mdi-check-circle',
            danger  : 'mdi-alert-circle',
            warning : 'mdi-alert',
        };
        const icon = iconMap[type] || 'mdi-information';

        let extra = '';
        if (studentName) extra += `<br><strong>Mahasiswa:</strong> ${studentName}`;
        if (nim)         extra += `&nbsp;&nbsp;<strong>NIM:</strong> ${nim}`;
        if (scanTime)    extra += `&nbsp;&nbsp;<strong>Waktu:</strong> ${scanTime}`;

        $('#scan-result')
            .html(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="mdi ${icon} me-2"></i>${message}${extra}
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>`)
            .show();

        // Auto-dismiss after 6s
        setTimeout(() => {
            $('#scan-result .alert').fadeOut(300, function () {
                $(this).remove();
                $('#scan-result').hide();
            });
        }, 6000);

        // Manual close
        $('#scan-result').off('click.alert').on('click.alert', '.btn-close', function () {
            $(this).closest('.alert').fadeOut(300, function () {
                $(this).remove();
                $('#scan-result').hide();
            });
        });
    }

    function appendRow(name, nim, scanTime) {
        if ($('#empty-state').is(':visible')) {
            $('#empty-state').hide();
            $('#table-wrapper').show();
        }

        const count = $('#attendance-tbody tr').length + 1;
        const row = `<tr class="new-row-flash">
            <td>${count}</td>
            <td>${name ?? '-'}</td>
            <td>${nim ?? '-'}</td>
            <td>${scanTime ?? '-'}</td>
            <td><span class="badge badge-success">Hadir</span></td>
        </tr>`;

        $('#attendance-tbody').prepend(row);

        const current = parseInt($('#attendance-count').text()) || 0;
        $('#attendance-count').text(current + 1);
    }

    function resetForm() {
        $('#serial_number').val('');
        $('#btn-submit').prop('disabled', true).html('<i class="mdi mdi-check-circle"></i> Catat Absensi');

        const $wrapper = $('#nfc-icon-wrapper');
        $wrapper.removeClass('scanning success error');
        $wrapper.find('.mdi').removeClass('text-success text-danger').addClass('text-primary');
    }

});
