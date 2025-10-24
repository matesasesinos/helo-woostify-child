<?php

//ciudades
function add_georef_autocomplete()
{
    if (!is_checkout())
        return;

    wp_enqueue_script('jquery-ui-autocomplete');

    wp_add_inline_script('jquery-ui-autocomplete', '
        jQuery(function($) {
            var citiesOptions = {
                source: function(request, response) {
                    $.getJSON(
                        "https://apis.datos.gob.ar/georef/api/localidades?nombre=" + request.term + "&max=10&campos=id,nombre,provincia.nombre",
                        function(data) {
                            response($.map(data.localidades, function(item) {
                                return {
                                    label: item.nombre + ", " + item.provincia.nombre,
                                    value: item.nombre,
                                    provincia: item.provincia.nombre
                                };
                            }));
                        }
                    );
                },
                minLength: 3,
                select: function(event, ui) {
                    $("#billing_state").val(ui.item.provincia);
                }
            }
            $("#billing_city, #shipping_city").autocomplete(citiesOptions);
        });
    ');
}
add_action('wp_enqueue_scripts', 'add_georef_autocomplete');
