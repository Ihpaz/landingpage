// function notification
function loaderNotification(url, redirect) {
    $.get(url, function (response, status, xhr) {
        if (xhr.status === 200) {
            var data = response.data;
            // Change total notification
            if (data.total_notification) {
                $('#messages_count').text('Terdapat ' + data.total_notification + ' pesan baru');
                $('#messages_notify').prepend('<span class="heartbit"></span> <span class="point"></span>');
            }
            // Add list notification
            $.each(data.notification, function (i, value) {
                $('#messages_list').prepend(
                    '<a href="' + redirect.replace(':id', value.id) + '">' +
                    '<div class="user-img"> <img src="' + value.img_thumbnail + '" alt="user"  style="width:40px;height:40px" class="img-circle"></div>' +
                    '<div class="mail-contnet">' +
                    '<h6>' + value.from + '</h6>' +
                    '<span class="mail-desc">' + value.message + '</span>' +
                    '<span class="time">' + value.human_created_at + '</span> </div>' +
                    '</a>'
                );
            });
            // Add counter span
            $.each(data.counter, function (i, value) {
                if (value.count) {
                    $('a[href="' + value.url + '"]:first').prepend('<span class="label label-rouded label-' + value.color + ' pull-right">' + value.count + '</span>');
                    var title_nav = $('a[href="' + value.url + '"]:first').parents('li').children('a[class="has-arrow waves-effect waves-dark"]:first');
                    if (title_nav.children('div[class="notify"]:first').length === 0) {
                        title_nav.append('<div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>');
                    }
                }
            });

            // Add notify alert
            $.each(data.notify, function (i, value) {
                if (value.notify) {
                    var title_nav = $('a[href="' + value.url + '"]:first').parents('li').children('a[class="has-arrow waves-effect waves-dark"]:first');
                    if (title_nav.children('div[class="notify"]:first').length === 0) {
                        title_nav.append('<div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>');
                    }
                }
            });
        }
    });
};

$(document).ready(function () {
    // Disable right-click
    $(this).bind("contextmenu", function(e) {
        // e.preventDefault();
    });
    // Adding some times to prevent multiple form submit
    $('form').on('submit', function () {
        $(this).find(':submit').prop('disabled', true);
        setTimeout(() => {
            $(this).find(':submit').prop('disabled', false);
        }, 5000);
    });
    // Prevent submit on modal
    $('.modal').on('shown.bs.modal', function () {
        $('form').on('submit', function () {
            $(this).find(':submit').prop('disabled', true);
            setTimeout(() => {
                $(this).find(':submit').prop('disabled', false);
            }, 5000);
        });
    });
    // Delay before alert is close
    $(".alert").delay(10000).slideUp(3000, function () {
        $(this).alert('close');
    });
    // Disabled button focus after close modal
    $('.modal').on('hidden.bs.modal', function (event) {
        event.stopImmediatePropagation();
    });
    $('#modal_edit_lg').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').children().remove();
    });
    // Pjax
    $(document).pjax('a', '#pjax-container', {
        timeout: 2000,
    });
    $.pjax.defaults.maxCacheLength = 0;
    $(document).on('pjax:complete', function() {
        refreshSidemenu();
        window.livewire.restart()
        $('pjax-stack-script').remove();
        $('pjax-stack-styles').remove();
    })
});

// Datatables language
$.extend($.fn.dataTable.defaults, {
    language: {
        "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
        "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
        "lengthMenu": "Tampilkan _MENU_ entri",
        "loadingRecords": "Sedang memuat...",
        "processing": "Sedang memproses...",
        "search": "Cari:",
        "zeroRecords": "Tidak ditemukan data yang sesuai",
        "thousands": "'",
        "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
        },
        "aria": {
            "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
            "sortDescending": ": aktifkan untuk mengurutkan kolom menurun"
        },
        "autoFill": {
            "cancel": "Batalkan",
            "fill": "Isi semua sel dengan <i>%d<\/i>",
            "fillHorizontal": "Isi sel secara horizontal",
            "fillVertical": "Isi sel secara vertikal"
        },
        "buttons": {
            "collection": "Kumpulan <span class='ui-button-icon-primary ui-icon ui-icon-triangle-1-s'\/>",
            "colvis": "Visibilitas Kolom",
            "colvisRestore": "Kembalikan visibilitas",
            "copy": "Salin",
            "copySuccess": {
                "1": "1 baris disalin ke papan klip",
                "_": "%d baris disalin ke papan klip"
            },
            "copyTitle": "Salin ke Papan klip",
            "csv": "CSV",
            "excel": "Excel",
            "pageLength": {
                "-1": "Tampilkan semua baris",
                "_": "Tampilkan %d baris"
            },
            "pdf": "PDF",
            "print": "Cetak",
            "copyKeys": "Tekan ctrl atau u2318 + C untuk menyalin tabel ke papan klip.<br \/><br \/>Untuk membatalkan, klik pesan ini atau tekan esc."
        },
        "searchBuilder": {
            "add": "Tambah Kondisi",
            "button": {
                "0": "Cari Builder",
                "_": "Cari Builder (%d)"
            },
            "clearAll": "Bersihkan Semua",
            "condition": "Kondisi",
            "data": "Data",
            "deleteTitle": "Hapus filter",
            "leftTitle": "Ke Kiri",
            "logicAnd": "Dan",
            "logicOr": "Atau",
            "rightTitle": "Ke Kanan",
            "title": {
                "0": "Cari Builder",
                "_": "Cari Builder (%d)"
            },
            "value": "Nilai",
            "conditions": {
                "date": {
                    "after": "Setelah",
                    "before": "Sebelum",
                    "between": "Diantara",
                    "empty": "Kosong",
                    "equals": "Sama dengan",
                    "not": "Tidak sama",
                    "notBetween": "Tidak diantara",
                    "notEmpty": "Tidak kosong"
                },
                "number": {
                    "between": "Diantara",
                    "empty": "Kosong",
                    "equals": "Sama dengan",
                    "gt": "Lebih besar dari",
                    "gte": "Lebih besar atau sama dengan",
                    "lt": "Lebih kecil dari",
                    "lte": "Lebih kecil atau sama dengan",
                    "not": "Tidak sama",
                    "notBetween": "Tidak diantara",
                    "notEmpty": "Tidak kosong"
                },
                "string": {
                    "contains": "Berisi",
                    "empty": "Kosong",
                    "endsWith": "Diakhiri dengan",
                    "equals": "Sama Dengan",
                    "not": "Tidak sama",
                    "notEmpty": "Tidak kosong",
                    "startsWith": "Diawali dengan"
                },
                "array": {
                    "equals": "Sama dengan",
                    "empty": "Kosong",
                    "contains": "Berisi",
                    "not": "Tidak",
                    "notEmpty": "Tidak kosong",
                    "without": "Tanpa"
                }
            }
        },
        "searchPanes": {
            "clearMessage": "Bersihkan Semua",
            "count": "{total}",
            "countFiltered": "{shown} ({total})",
            "title": "Filter Aktif - %d",
            "collapse": {
                "0": "Panel Pencarian",
                "_": "Panel Pencarian (%d)"
            },
            "emptyPanes": "Tidak Ada Panel Pencarian",
            "loadMessage": "Memuat Panel Pencarian"
        },
        "infoThousands": ",",
        "select": {
            "cells": {
                "1": "1 sel terpilih",
                "_": "%d sel terpilih"
            },
            "columns": {
                "1": "1 kolom terpilih",
                "_": "%d kolom terpilih"
            }
        },
        "datetime": {
            "previous": "Sebelumnya",
            "next": "Selanjutnya",
            "hours": "Jam",
            "minutes": "Menit",
            "seconds": "Detik",
            "unknown": "-",
            "amPm": [
                "am",
                "pm"
            ],
            "weekdays": [
                "Min",
                "Sen",
                "Sel",
                "Rab",
                "Kam",
                "Jum",
                "Sab"
            ],
            "months": [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember"
            ]
        },
        "editor": {
            "close": "Tutup",
            "create": {
                "button": "Tambah",
                "submit": "Tambah",
                "title": "Tambah inputan baru"
            },
            "remove": {
                "button": "Hapus",
                "submit": "Hapus",
                "confirm": {
                    "_": "Apakah Anda yakin untuk menghapus %d baris?",
                    "1": "Apakah Anda yakin untuk menghapus 1 baris?"
                },
                "title": "Hapus inputan"
            },
            "multi": {
                "title": "Beberapa Nilai",
                "info": "Item yang dipilih berisi nilai yang berbeda untuk input ini. Untuk mengedit dan mengatur semua item untuk input ini ke nilai yang sama, klik atau tekan di sini, jika tidak maka akan mempertahankan nilai masing-masing.",
                "restore": "Batalkan Perubahan",
                "noMulti": "Masukan ini dapat diubah satu per satu, tetapi bukan bagian dari grup."
            },
            "edit": {
                "title": "Edit inputan",
                "submit": "Edit",
                "button": "Edit"
            },
            "error": {
                "system": "Terjadi kesalahan pada system. (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Informasi Selebihnya<\/a>)."
            }
        },
        "stateRestore": {
            "creationModal": {
                "button": "Buat",
                "columns": {
                    "search": "Pencarian Kolom",
                    "visible": "Visibilitas Kolom"
                },
                "name": "Nama:",
                "order": "Penyortiran",
                "paging": "Penomoran",
                "scroller": "Posisi Scroll",
                "search": "Pencarian",
                "searchBuilder": "Pembangun Pencarian",
                "select": "Pemilihan",
                "title": "Buat State Baru",
                "toggleLabel": "Termasuk:"
            },
            "duplicateError": "State dengan nama ini sudah ada.",
            "emptyError": "Nama tidak boleh kosong.",
            "emptyStates": "Tidak ada state yang disimpan.",
            "removeConfirm": "Apakah Anda yakin ingin menghapus %s?",
            "removeError": "Gagal menghapus state.",
            "removeJoiner": "dan",
            "removeSubmit": "Hapus",
            "removeTitle": "Hapus State",
            "renameButton": "Ganti Nama",
            "renameLabel": "Nama Baru untuk %s:",
            "renameTitle": "Ganti Nama State"
        }
    }
});

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

function showModalDelete(name, url) {
    $("#modal_delete_title").text("Hapus data '" + name + "'");
    $("#modal_delete_form").attr("action", url);
    $("#modal_delete").modal("show");
}

function showModalDetail(url) {
    $.ajax({
        url: url,
        dataType: 'html',
        success: function (res) {
            $("#modal_show").find('.modal-content').html(res);
            $("#modal_show").modal("show");
        }
    });
}

function showModalDetailLg(url) {
    $.ajax({
        url: url,
        dataType: 'html',
        success: function (res) {
            $("#modal_show_lg").find('.modal-content').html(res);
            $("#modal_show_lg").modal("show");
        }
    });
}

function showModalDetailXl(url) {
    $.ajax({
        url: url,
        dataType: 'html',
        success: function (res) {
            $("#modal_show_xl").find('.modal-content').html(res);
            $("#modal_show_xl").modal("show");
        }
    });
}

function showModalEdit(url) {
    $.ajax({
        url: url,
        dataType: 'html',
        success: function (res) {
            $("#modal_edit").find('.modal-content').html(res);
            $("#modal_edit").modal("show");
        }
    });
}

function showModalEditLg(url) {
    $.ajax({
        url: url,
        dataType: 'html',
        success: function (res) {
            $("#modal_edit_lg").find('.modal-content').html(res);
            $("#modal_edit_lg").modal("show");
        }
    });
}

function showModalEditXl(url) {
    $.ajax({
        url: url,
        dataType: 'html',
        success: function (res) {
            $("#modal_edit_xl").find('.modal-content').html(res);
            $("#modal_edit_xl").modal("show");
        }
    });
}

function showModalEmbedXl(url, mime) {
    var object = '<object data="' + url + '" type="' + mime + '" width="100%" frameborder="0"></object>';
    $("#modal_embed_object").html(object);
    $("#modal_embed_xl").modal("show");
}

function showModalEmbedXlHeight(url, mime, height = 600) {
    var object = '<object data="' + url + '" type="' + mime + '" width="100%" height="' + height + 'px" frameborder="0"></object>';
    $("#modal_embed_object").html(object);
    $("#modal_embed_xl").modal("show");
}

function refreshTable(id) {
    $('#' + id).DataTable().ajax.reload();
}

function compareDates(d1, d2) {
    dateFirst = d1.split("/");
    dateSecond = d2.split("/");
    var date1 = new Date(
        parseInt(dateFirst[2]),
        parseInt(dateFirst[1]) - 1,
        parseInt(dateFirst[0])
    );
    var date2 = new Date(
        parseInt(dateSecond[2]),
        parseInt(dateSecond[1]) - 1,
        parseInt(dateSecond[0])
    );
    return date1 >= date2;
}

function refreshSidemenu() {
    var url = window.location;
    $('#sidebarnav .active').removeClass('active');
    var element = $('ul#sidebarnav a').filter(function () {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().addClass('active');
    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent().addClass('active');
        }
        else {
            break;
        }
    }
}

function refreshForm() {
    $("[rel=select2]").select2({});
    $("[rel=taginput]").select2({
        tags: true,
        tokenSeparators: [","],
    });

    $(".input-group.date").datetimepicker({
        format: "DD/MM/YYYY",
    });

    $(".input-group.datetime").datetimepicker({
        format: "DD/MM/YYYY LT",
        sideBySide: true,
    });
}
