/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
//start the Stimulus application
import './bootstrap';
import Swal from "sweetalert2";
const $ = require("jquery");
global.$ = global.jQuery = $;
require('bootstrap_js');
require('datatables.net-bs5');
require('countup.js');
require('onscreen');
require('jarallax');
require('vanillajs-datepicker');
require('nouislider');
require('notyf');
require('simplebar');
require('@fortawesome/fontawesome-free');
require('waypoints/lib/noframework.waypoints.min.js');
require('datatables.net-autofill-bs5');
require('datatables.net-buttons-bs5');
require('datatables.net-scroller-bs5');
require('datatables.net-select-bs5');
require('select2');
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
require('./js/volt.js');
$(document).ready(function () {
    var table = $('#example').DataTable({
        lengthChange: false,
        columnDefs: [{targets: [1,-2], className: "truncate dt-center"}],
        ordering: false,
        autoWidth: false,
        responsive: true
    });
    $('.js-example-basic-multiple').select2();
    $('.js-example-basic-single').select2();
    $('.js-example-placeholder-single').select2({
        placeholder: "Select a category",
        allowClear: true
    });
    $("[name='editor[country]']").select2();

});
window.createCalendar = function createCalendar(events) {
    var calendarEl = document.getElementById('calendar');

    const calendar = new Calendar(calendarEl, {
        plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin ],
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        timeZone:"CET",
        height:600,
        initialDate: Date.now(),
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        dayMaxEvents: true, // allow "more" link when too many events
        events:events,
    });

    calendar.render();
    return calendar;
}
window.dialogue = function dialogue(index, route,redirectPath=null) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("Form submitted");
            $.ajax({
                type: "POST",
                url: route,
                data: null,
                success: function (response) {
                    if (response === "Deleted Successfully") {
                        swalWithBootstrapButtons.fire(
                            'Deleted!',
                            'Your data has been deleted.',
                            'success'
                        ).then(r =>{
                            if(redirectPath===null)
                            {
                                $('#example').DataTable().row(index).remove().draw();
                            }
                            else{
                                console.log("Redirecting to "+redirectPath);
                                window.location.replace(redirectPath)
                            }
                        } );

                    } else {
                        swalWithBootstrapButtons.fire(
                            'Error!',
                            'There was something wrong.',
                            'error'
                        );
                    }
                },
                error: function (message) {
                    console.log(message);
                }
            });

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Nothing was done!',
                'error'
            )
        }
    });

}
window.dialogueBorrowing = function dialogueBorrowing(index, route,redirectPath=null) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: route,
                data: null,
                success: function (response) {
                    ajaxResponse(response,swalWithBootstrapButtons,index,redirectPath);
                },
                error: function (message) {
                    console.log(message);
                }
            });

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Nothing was done !',
                'error'
            )
        }
    });

}

function ajaxResponse(response,swalWithBootstrapButtons,index,redirectPath)
{
    if (response === "Borrowed Successfully") {
        swalWithBootstrapButtons.fire(
            'Borrowed Successfully!',
            'The ordered book was successfully borrowed.',
            'success'
        ).then(r =>{
            if(redirectPath===null)
            {
                const cell = $('#example').DataTable().cell(index,6);
                cell.data('<div style="color:blue">Borrowed - Not Returned Yet</div>').draw();
            }
            else{
                console.log("Redirecting to "+redirectPath);
                window.location.replace(redirectPath)
            }
        } );

    }
    else if (response === "Declined Successfully") {
        swalWithBootstrapButtons.fire(
            'Borrowing Declined!',
            'The borrow request was declined successfully.',
            'success'
        ).then(r =>{
            if(redirectPath===null)
            {
                const cell = $('#example').DataTable().cell(index,6);
                cell.data('<div style="color:red">Declined</div>').draw();
            }
            else{
                console.log("Redirecting to "+redirectPath);
                window.location.replace(redirectPath)
            }
        } );

    }
    else {
        swalWithBootstrapButtons.fire(
            'Error!',
            'There was something wrong.',
            'error'
        );
    }

}

