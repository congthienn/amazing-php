<?php
    include_once __DIR__ . '/../../../Amazing-PHP/backend/connect_db.php';
    if(session_id()===""){
        session_start();
    }
    $product_id = $_GET['product_id'];
    $quantity = $_GET['quantity'];
    $sql_select_product = <<<EOT
        SELECT * FROM hanghoa WHERE MSHH = '$product_id';
    EOT;
    $query_product = mysqli_query($conn,$sql_select_product);
    $result_product = mysqli_fetch_array($query_product,MYSQLI_ASSOC);
    if($result_product['SoLuongHang'] < $quantity){
        echo json_encode($result_product['SoLuongHang']);
    }else{
        if(!isset($_SESSION['cart'])){
            $data_cart[$product_id] = array(
                'product_name' => $result_product['TenHH'],
                'product_price' => $result_product['Gia'],
                'product_quantity' => $quantity,
                'product_img' => $result_product['HinhDaiDien']
            );
            $_SESSION['quantity_cart'] = $quantity;
            $_SESSION['cart'] = $data_cart;
        }else{
            $data_cart = $_SESSION['cart'];
            if(isset($data_cart[$product_id])){
                $data_cart[$product_id]['product_quantity'] += $quantity;
                $_SESSION['cart'] = $data_cart;
                $_SESSION['quantity_cart'] += $quantity;
            }else{
                $data_cart[$product_id] = array(
                    'product_name' => $result_product['TenHH'],
                    'product_price' => $result_product['Gia'],
                    'product_quantity' => $quantity,
                    'product_img' => $result_product['HinhDaiDien']
                );
                $_SESSION['cart'] = $data_cart; 
                $_SESSION['quantity_cart'] += $quantity; 
            }
        }
    }
    setcookie("Cart",json_encode($data_cart),time()+(30*24*3600),'/');
    $data_result = [];
    $sum_money = 0;
    $result_cart_header = '
    <div class="container_cart">
        <div class="cart_header--title">
            Giỏ hàng của bạn
        </div>
        <div class="content_cart">';
            foreach($data_cart as $val=>$product_item){
            $sum_money += $product_item['product_quantity']*$product_item['product_price'];
            $result_cart_header .= '
                <div class="product_cart--item">
                    <img src="/../Amazing-PHP/assets/uploads/products/'.$product_item['product_name'].'/'.$product_item['product_img'].'" width="90px">
                    <div class="product_cart--item__infor">
                        <div class="cart_product--name">'.$product_item['product_name'].'</div>
                        <div class="cart_product--price">'.number_format($product_item['product_price'],0,',','.').'đ</div>
                        <div class="cart_product--quantity">
                            <input type="button" value="-" id="" class="btn_cart_quantity btn_cart_product--reduce">
                            <input type="text" value="'.$product_item['product_quantity'].'" class="value_cart_product--quantity" readonly>
                            <input type="button" value="+" id="" class="btn_cart_quantity btn_cart_product--increase">
                        </div>
                        <div class="cart_product--delete">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                </div>';
            }
            $result_cart_header .='
        </div>
        <div class="sum_money_cart">
            <div>
                <strong>Tổng tiền </strong>
            </div>
            <div>'.number_format($sum_money,0,',','.').'đ</div>
        </div>
        <div class="button_cart">
            <div>
                <a href="" class="button_cart--item pay_now">Tiến hành thanh toán</a>
            </div>
            <div>
                <a href="" class="button_cart--item go_cart">Đi đến giỏ hàng</a>
            </div>   
        </div>
    </div>';
    $quantity_cart = $_SESSION['quantity_cart'];
    $data_result['quantity_cart'] = $quantity_cart;
    $data_result['cart_header'] = $result_cart_header;
    echo json_encode($data_result);
?>