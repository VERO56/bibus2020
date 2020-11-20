/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
//require('../css/app.css');
import '../css/admin.css';
import 'bootstrap';
import 'select2';
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
import '@fortawesome/fontawesome-free/js/fontawesome'
import '@fortawesome/fontawesome-free/js/solid'
import '@fortawesome/fontawesome-free/js/regular'
import '@fortawesome/fontawesome-free/js/brands'
require('jquery-ui');
require('bootstrap-typeahead');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
$.noConflict();
global.$ = global.jQuery = $;


//console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

jQuery(document).ready(function () {
    var $wrapper = $('.js-genus-scientist-wrapper');
    $wrapper.on('click', '.js-remove-scientist', function (e) {
        e.preventDefault();
        $(this).closest('.js-genus-scientist-item')
            .fadeOut()
            .remove();
    });
    $wrapper.on('click', '.js-genus-scientist-add', function (e) {
        e.preventDefault();
        // Get the data-prototype explained earlier
        var prototype = $wrapper.data('prototype');
        // get the new index
        var index = $wrapper.data('index');
        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);
        // increase the index with one for the next item
        $wrapper.data('index', index + 1);
        // Display the form in the page before the "new" link
        $(this).before(newForm);
    });

    $('.custom-file-input').on('change', function (event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    });

    $(document).on('change', '.custom-file-input', function () {
        let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
    });

    $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    $('.js-select2-pro').select2({
        placeholder: 'Saisir ou choisir un pro',
        allowClear: true,
        language: 'fr',
        theme: 'bootstrap4',
    });

    // $(".toggle-button").toggleButton();
});
$(function () {
    var input = $('#search-input');
    var suggestUrl = input.data('suggest');

    input.typeahead({
        minLength: 2, // will start autocomplete after 2 characters
        items: 5, // will display 5 suggestions
        highlighter: function (item) {
            var elem = this.reversed[item];
            var html = '<td class="typeahead">';

            if (elem.name) {
                html += '<td class="suggestion">' + elem.name + '</td>';
            }

            html += '</td>';

            return html;
        },
        source: function (query, process) {
            // "query" is the search string
            // "process" is a closure which must receive the suggestions list

            var $this = this;
            $.ajax({
                url: suggestUrl,
                type: 'GET',
                data: {
                    q: query
                },
                success: function (data) {
                    //  "name" is the string to display in the suggestions

                    // this "reversed" array keep a temporarly relation between each suggestion and its data
                    var reversed = {};

                    // here we simply generate the suggestions list
                    var suggests = [];

                    $.each(data, function (id, elem) {
                        reversed[elem.name] = elem;
                        suggests.push(elem.name);
                    });

                    $this.reversed = reversed;

                    // displaying the suggestions
                    process(suggests);
                }
            })
        },
        // this method is called when a suggestion is selected
        updater: function (item) {
            // do whatever you want

            return elem.name; // this return statement fills the input with the selected string
        },
        // this method determines which of the suggestions are valid. We are already doing all this server side, so here just
        // return "true"
        matcher: function () {
            return true;
        }
    });

    var table = $("#table_id").DataTable({
        responsive: true,
        dom: '<"wrapper"fBtlip>',
        buttons: [
            { extend: 'excel', className: 'excelButton', text: 'Export <i class="far fa-file-excel" aria-hidden="true"></i>' },
            {
                extend: 'pdf', className: 'pdfButton', text: 'Export <i class="far fa-file-pdf" aria-hidden="true"></i>', orientation: 'landscape',
                pageSize: 'A4',
            },
            { extend: 'print', className: 'copyButton', text: '<i class="fas fa-print" aria-hidden="true"></i>' },
            { extend: 'colvis', className: 'buttons-collection buttons-colvis', text: 'Colonnes visibles', postfixButtons: ['colvisRestore'] },
        ],

        language: {
            processing: "Traitement en cours...",
            search: "Rechercher&nbsp;:",
            lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
            info: "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty: "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix: "",
            loadingRecords: "Chargement en cours...",
            zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable: "Aucune donnée disponible dans le tableau",
            paginate: {
                first: "Premier",
                previous: "Pr&eacute;c&eacute;dent",
                next: "Suivant",
                last: "Dernier"
            },
        },

    });
    table.buttons().container()
        .appendTo('#example_wrapper .col-md-6:eq(0)');

});
jQuery(document).ready(function () {
    var $customer = $('#maintenance_customer');
    // When sport gets selected ...
    $customer.change(function () {

        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$customer.attr('name')] = $customer.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                // Replace current position field ...
                $('#maintenance_products').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#maintenance_products')
                );
                // Position field now displays the appropriate positions.

            }
        });


    });
});
jQuery(document).ready(function () {
    var $product = $('#sav_product');
    // When sport gets selected ...
    $product.change(function () {

        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$product.attr('orderNumber')] = $product.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                // Replace current position field ...
                $('#sav_customer').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#sav_customer')
                );
                // Position field now displays the appropriate positions.

            }
        });


    });

});
