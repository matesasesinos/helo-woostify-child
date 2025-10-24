<?php

add_action('admin_init', 'helo_mw_register_settings');

function helo_mw_register_settings()
{
    // Registra la opción para el checkbox
    register_setting('general', 'helo_mw_option', array(
        'type' => 'boolean',
        'sanitize_callback' => 'helo_mw_sanitize_checkbox',
        'default' => false,
    ));

    // Registra la opción para el campo de texto (peso máximo)
    register_setting('general', 'helo_mw_option_weight', array(
        'type' => 'string',
        'sanitize_callback' => 'helo_mw_sanitize_weight',
        'default' => '',
    ));

    // Añade una sección de configuración
    add_settings_section(
        'helo_mw_section', // ID de la sección
        'Configuración de Peso Máximo por Pedido', // Título de la sección
        'helo_mw_section_callback', // Callback para mostrar el contenido de la sección
        'general' // Página donde se mostrará (Opciones Generales)
    );

    // Añade el campo del checkbox
    add_settings_field(
        'helo_mw_option', // ID del campo
        'Activar límite de peso', // Título del campo
        'helo_mw_checkbox_callback', // Callback para mostrar el campo
        'general', // Página donde se mostrará
        'helo_mw_section' // Sección a la que pertenece
    );

    // Añade el campo de texto para el peso máximo
    add_settings_field(
        'helo_mw_option_weight', // ID del campo
        'Peso máximo permitido (kg)', // Título del campo
        'helo_mw_weight_callback', // Callback para mostrar el campo
        'general', // Página donde se mostrará
        'helo_mw_section' // Sección a la que pertenece
    );

    // Registra la opción para el campo de texto (mensaje de error)
    register_setting('general', 'helo_mw_option_error_message', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ));

    // Añade una sección de configuración
    add_settings_section(
        'helo_mw_section', // ID de la sección
        'Configuración de Peso Máximo por Pedido', // Título de la sección
        'helo_mw_section_callback', // Callback para mostrar el contenido de la sección
        'general' // Página donde se mostrará (Opciones Generales)
    );

    // Añade el campo de texto para el mensaje de error
    add_settings_field(
        'helo_mw_option_error_message', // ID del campo
        'Mensaje de error', // Título del campo
        'helo_mw_error_message_callback', // Callback para mostrar el campo
        'general', // Página donde se mostrará
        'helo_mw_section' // Sección a la que pertenece
    );
}

// Callback para mostrar la descripción de la sección
function helo_mw_section_callback()
{
    echo '<p>Configura el límite de peso máximo permitido por pedido.</p>';
}

// Callback para mostrar el campo del checkbox
function helo_mw_checkbox_callback()
{
    $option = get_option('helo_mw_option', false);
    echo '<input type="checkbox" id="helo_mw_option" name="helo_mw_option" value="1" ' . checked(1, $option, false) . ' />';
    echo '<label for="helo_mw_option"> Activar límite de peso</label>';
}

// Callback para mostrar el campo de texto (peso máximo)
function helo_mw_weight_callback()
{
    $weight = get_option('helo_mw_option_weight', '25');
    echo '<input type="number" id="helo_mw_option_weight" name="helo_mw_option_weight" value="' . esc_attr($weight) . '" />';
    echo '<p class="description">Introduce el peso máximo permitido en kilogramos (kg).</p>';
}

// Callback para mostrar el campo de texto (mensaje de error)
function helo_mw_error_message_callback()
{
    $error_message = get_option('helo_mw_option_error_message', '');
    echo '<textarea id="helo_mw_option_error_message" name="helo_mw_option_error_message" rows="4" cols="50">' . esc_textarea($error_message) . '</textarea>';
    echo '<p class="description">Introduce el mensaje de error que se mostrará si se supera el límite de peso. Usa <code>%s</code> para incluir el límite de peso en el mensaje.</p>';
}

// Sanitización del checkbox
function helo_mw_sanitize_checkbox($input)
{
    return isset($input) ? true : false;
}

// Sanitización del campo de texto (peso máximo)
function helo_mw_sanitize_weight($input)
{
    return is_numeric($input) ? floatval($input) : '';
}

// agregame una opcion que sea para agrear un texto plano en la pagina de Ajustes > Generales que el titulo sea "Mensaje de Top Bar"
function helo_mw_top_bar_message_callback()
{
    $top_bar_message = get_option('helo_mw_top_bar_message', 'Envíos a todo el país - 10% de descuento más de 5L - Somos fabricantes - Con tu primera compra, asesoramiento gratuito');
    echo '<textarea id="helo_mw_top_bar_message" name="helo_mw_top_bar_message" rows="4" cols="50">' . esc_textarea($top_bar_message) . '</textarea>';
    echo '<p class="description">Introduce el mensaje que se mostrará en la parte superior de la página.</p>';
}
add_action('admin_init', 'helo_mw_register_top_bar_message_setting');
function helo_mw_register_top_bar_message_setting()
{
    register_setting('general', 'helo_mw_top_bar_message', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ));

    add_settings_field(
        'helo_mw_top_bar_message', // ID del campo
        'Mensaje de Top Bar', // Título del campo
        'helo_mw_top_bar_message_callback', // Callback para mostrar el campo
        'general', // Página donde se mostrará
        'default' // Sección a la que pertenece
    );
}
