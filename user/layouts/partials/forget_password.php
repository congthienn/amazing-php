<?php
    include_once __DIR__ . '/../../../admin/connect_db.php';
    require_once __DIR__.'/../../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    function inputdata($data){
        $data = trim($data);
        $data = htmlspecialchars($data);
        $data = stripcslashes($data);
        return $data;
    }
    function rand_string(){
        $str='';
        $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@$&#";
        $size = strlen($char);
        for($i=0;$i<10;$i++){
            $str .=$char[rand(0,$size-1)];
        }
        return $str;
    }
    $customer_password = rand_string();
    $customer_password_encode = sha1(sha1(md5(md5(sha1($customer_password)))));
    $email = inputdata($_GET['email_user']);
    $sql_select_customer = <<<EOT
        SELECT * FROM khachhang WHERE EmailKH = '$email'
    EOT;
    $query_customer = mysqli_query($conn,$sql_select_customer);
    $customer = mysqli_fetch_array($query_customer,MYSQLI_ASSOC);

    if(($customer == 0) && ($staff == 0)){
        echo json_encode("error");
    }else if($customer > 0){
        $sql_updatePassword = <<<EOT
            UPDATE khachhang SET Password = '$customer_password_encode' WHERE EmailKH = '$email';
        EOT;
        mysqli_query($conn,$sql_updatePassword);
        $mail = new PHPMailer(true);  
        try {
            $mail->SMTPDebug = 2;       
            $mail->isSMTP();                           
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;                             
            $mail->Username = 'congthienn1601@gmail.com'; 
            $mail->Password = 'nqhqshsiteocbyul';                 
            $mail->SMTPSecure =  PHPMailer::ENCRYPTION_SMTPS;                             
            $mail->Port = 465;                                      
            $mail->CharSet = "UTF-8";
            $mail->setFrom('Amazing@gmail.com', 'Amazing');
            $mail->addAddress($email);              
            $mail->addReplyTo('congthienn1601@gmail.com');
            $mail->isHTML(true);                                    
            $mail->Subject = "Thông báo cấp lại mật khẩu tài khoản người dùng trên Amazing";         
            $body = '
                <div style="display: flex;justify-content: center; font-size: 17px;position: relative;top: 50%;transform: translateY(-60%);">
                    <div>
                        <div style="border: 1px solid black;min-height: 200px;display: inline-block;padding: 20px 20px 40px 20px;border-radius: 5px;">
                            <div style="margin: 10px 0 20px;"><span style="font-size: 20px;">Amazing xin chào,</span></div>
                                <div>Xin chào <span style="font-weight: 700;">'.$customer['HoTenKH'].'</span>, mật khẩu của bạn vừa được đặt lại thành công trên Amazing !</div>
                                <div style="margin: 5px 0;">Đây là mật khẩu đăng nhập tài khoản của bạn:</div>
                                <div style="font-size: 15px;font-style: italic;">(Bạn vui lòng không cung cấp mật khẩu này cho ai khác)</div>
                                <div style="display: flex;justify-content: center;align-items: center;">
                                <div style="border: 1px lightgray solid;display: inline-block;padding: 10px;margin: 30px 0;border-radius: 4px;font-weight: 600;color: blue;">
                                '.$customer_password.'
                                </div>
                            </div>
                            <div><span style="font-style: italic;">Copyright &copy; <i class="far fa-copyright"></i> Amazing 2020-2021</span></div>
                        </div>
                    </div>
                </div>
            ';
            $mail->Body = $body;
            $mail->send();
        } catch (Exception $e) {
            echo 'Lỗi khi gởi mail: ', $mail->ErrorInfo;
        }
    }
?>