$(document).ready(function(){
    $(".btn_cart_quantity").click(function(){
        var product_id = $(this).data("product_id");
        var act = $(this).data("act");
        var that = $(this);
    $.ajax({
        type: "GET",
        url: "/../Amazing-PHP/frontend/layouts/partials/up_down_quantity.php",
        data:{
            product_id,act
        },
        dataType: "json",
        success: function (response) {
            $('#result_sum_money_cart ,#sum_money').html(response.sum_money);
            that.parent().find(".value_cart_product--quantity").prop("value",response.product_quantity);
            $("."+product_id).find(".value_cart_product--quantity").prop("value",response.product_quantity);
            $("#result_quantity_cart").html('<div class="quantity_cart">'+response.quantity_cart_header+'</div>');
            that.parent().parent().parent().find("#money_abc").html('<strong>Thành tiền : </strong>'+response.item_sum);
        }
    });
    });
});