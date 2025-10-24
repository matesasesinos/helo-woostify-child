(function ($) {
  //Comportamiento de categorías
  $(document).ready(function () {
    $(".wc-block-product-categories-list-item > a").on("click", function (e) {
      // Solo prevenir el comportamiento por defecto si este enlace tiene un submenu justo después
      if ($(this).next("ul").length > 0) {
        e.preventDefault();
        $(this).next("ul").slideToggle();
      }
    });
  });

  $(document).ready(function () {
    $("#save-customer-address").on("click", function (e) {
      e.preventDefault();

      const address1 = $("#customer_address");
      let address2 =
        $("#customer_address2").length == 1 ? $("#customer_address2") : false;
      const city = $("#customer_city");
      const zip = $("#customer_zip");
      const state = $("#customer_state");
      const order = $("#order_id").val();
      const customer = $("#customer_id").val();

      let validateFields = [address1, city, zip, state];

      let validate = true;

      validateFields.forEach((item) => {
        if (item.val().length < 1) {
          item.css("border-color", "red");
          validate = false;
        }
      });

      if (!validate) return;

      var data = {
        address1: address1.val(),
        address2: address2 ? address2.val() : "",
        zip: zip.val(),
        city: city.val(),
        order: order,
        state: state.val(),
        action: "update_customer_address",
      };

      $.ajax({
        type: "POST",
        url: helo.url,
        data: data,
        beforeSend: function () {
          $("#save-customer-address")
            .text("Guardando...")
            .prop("disabled", true);

          address1.prop("disabled", true);
          state.prop("disabled", true);
          city.prop("disabled", true);
          zip.prop("disabled", true);
        },
        success: function (response) {
          if (!response.success) {
            alert(response.data);
            address1.prop("disabled", false);
            state.prop("disabled", false);
            city.prop("disabled", false);
            zip.prop("disabled", false);
            $("#save-customer-address")
              .text("Guardar dirección")
              .prop("disabled", false);
            return;
          }

          alert(response.data);
          window.location.reload();
        },
        error: function (e) {
          console.log(e);
        },
      });
    });
  });

})(jQuery);
