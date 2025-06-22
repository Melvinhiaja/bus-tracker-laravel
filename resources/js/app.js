import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
import $ from 'jquery';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import Swal from 'sweetalert2';

$(document).ready(function () {
    var calendarEl = document.getElementById("calendar");
    if (calendarEl) {
        var calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: "dayGridMonth",
            locale: "id",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay"
            },
            editable: true,
            selectable: true,
            selectMirror: true,

            // Pilih beberapa hari sekaligus
            select: function (info) {
                Swal.fire({
                    title: "Tambah Event",
                    html: `
                        <input id="event-title" class="swal2-input" placeholder="Nama Event">
                        <input id="event-color" class="swal2-input" type="color" value="#3498db">
                    `,
                    showCancelButton: true,
                    confirmButtonText: "Simpan",
                    cancelButtonText: "Batal",
                    preConfirm: () => {
                        let title = document.getElementById("event-title").value;
                        let color = document.getElementById("event-color").value;
                        if (!title) {
                            Swal.showValidationMessage("Nama event harus diisi!");
                            return false;
                        }
                        return { title, color };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/events",
                            method: "POST",
                            data: {
                                title: result.value.title,
                                start: info.startStr,
                                end: info.endStr,
                                color: result.value.color
                            },
                            success: function () {
                                Swal.fire("Berhasil!", "Event telah ditambahkan.", "success");
                                calendar.refetchEvents();
                            }
                        });
                    }
                });
                calendar.unselect();
            },

            // Ambil event dari API
            events: "/api/events",

            // Edit event dengan drag & drop
            eventDrop: function (info) {
                $.ajax({
                    url: "/api/events/" + info.event.id,
                    method: "PUT",
                    data: {
                        start: info.event.startStr,
                        end: info.event.endStr
                    },
                    success: function () {
                        Swal.fire("Diperbarui!", "Event telah diperbarui.", "success");
                    }
                });
            },

            // Hapus event dengan klik
            eventClick: function (info) {
                Swal.fire({
                    title: "Hapus Event?",
                    text: "Event akan dihapus secara permanen.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/events/" + info.event.id,
                            method: "DELETE",
                            success: function () {
                                Swal.fire("Dihapus!", "Event telah dihapus.", "success");
                                calendar.refetchEvents();
                            }
                        });
                    }
                });
            }
        });

        calendar.render();
    }
});

