<?php

// database connections test
    $host = 'localhost';
    $username = 'erp';
    $password = 'A%O~Lm5xSNE4';
    $conn = new PDO("mysql:host=$host;dbname=erp;charset=utf8mb4",$username,$password,array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    $site_url = "http://localhost/";

// database connections true
    // $host = 'localhost';
    // $username = 'root';
    // $password = '';
    // $conn = new PDO("mysql:host=$host;dbname=",$username,$password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    // $site_url = "http://localhost/avioschool/";


// Time modules
    include ("controlls/db/jdf.php");
    date_default_timezone_set("Asia/Tehran");
    $fa_date = tr_num(jdate('Y/m/d'));
    $en_time = date('Y-m-d');
    $current_time = date("H:i");


// Session control
    if(session_id()==''){
        session_start();
    }else{
    $user_info=$_SESSION['user_login'];
    }

?>





<?php
    if(isset($_SESSION['user_login'])){
    $user_access=$_SESSION['user_login'];
    $user_id_sess = $user_access["id"];
      try{
        $user_show_me = $conn->prepare("select * from users where id = :id");
        $user_show_me->bindParam(':id', $user_id_sess);
        $user_show_me->execute();
        $users = $user_show_me->fetchAll(PDO::FETCH_OBJ);
      }catch(Exception $e){
        echo  $e->getMessage();
      }
      foreach($users as $users_info_show)
      $my_profile_id = $users_info_show->id;
      $my_profile_phone_number = $users_info_show->phone_number;
      $my_profile_chat_id = $users_info_show->chat_id;
      $my_profile_email = $users_info_show->email;
      $my_profile_name = $users_info_show->name;
      $my_profile_role = $users_info_show->role;
      $my_profile_nation_code = $users_info_show->nation_code;
      $my_profile_avatar = $users_info_show->avatar;
      $my_profile_place = $users_info_show->place;
      $my_profile_adress = $users_info_show->adress;
    }

      ?>





<?php
    // if(isset($_COOKIE['bjslcnr'])){
    // $user_id_sess = $_COOKIE['bjslcnr'];
    //   try{
    //     $user_show_me = $conn->prepare("select * from users where id = :id");
    //     $user_show_me->bindParam(':id', $user_id_sess);
    //     $user_show_me->execute();
    //     $users = $user_show_me->fetchAll(PDO::FETCH_OBJ);
    //   }catch(Exception $e){
    //     echo  $e->getMessage();
    //   }
    //   foreach($users as $users_info_show)
    //   $my_profile_id = $users_info_show->id;
    //   $my_profile_phone_number = $users_info_show->phone_number;
    //   $my_profile_name = $users_info_show->name;
    //   $my_profile_role = $users_info_show->role;
    //   $my_profile_nation_code = $users_info_show->nation_code;
    //   $my_profile_avatar = $users_info_show->avatar;
    //   $my_profile_place = $users_info_show->place;
    //   $my_profile_adress = $users_info_show->adress;
    // }

      ?>




<?php
try{
$users_show = $conn->query("select * from settings where id=1");
$users = $users_show->fetchAll(PDO::FETCH_OBJ);
}catch(Exception $e){
echo  $e->getMessage();
}
?>

<?php foreach($users as $settings_item) :?>
  <?php 
  $setting_site_name = $settings_item->site_name;
  $setting_site_logo = $settings_item->site_logo;
  $setting_site_description = $settings_item->site_description;
  $setting_support_number = $settings_item->support_number;
  $setting_about_page = $settings_item->about_page;
  $setting_support_page = $settings_item->support_page;
  $setting_questions_page = $settings_item->questions_page;
  ?>
<?php endforeach; ?>



<?php
// Define the function
function tgsend($message) {
    $message = urlencode($message); // Encode the message for use in a URL
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU"; // Replace with your actual bot token
    $chat_id = "-1002470002481"; // Replace with your actual chat ID
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$message";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
}



function Tele($user_id, $text) {
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // Get user info from the database using chat_id
    global $conn;
    $stmt = $conn->prepare("SELECT name FROM users WHERE chat_id = :chat_id");
    $stmt->execute(['chat_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
        $name = $user->name;

        // Construct the message with proper emoji
        $message = "Hi *$name* 👋\n\n$text\n-------------\n*This broadcast message is sent automatically by the bot, please don't reply to it!*";

        // Prepare data to send to Telegram API
        $data = [
            "chat_id" => $user_id,
            "text" => $message,
            "parse_mode" => "Markdown"
        ];

        // Send the request to Telegram API using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    } else {
        echo "User not found!";
        return false;
    }
}


function Telelogin($user_id, $text) {
    $token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // Get user info from the database using chat_id
    global $conn;
    $stmt = $conn->prepare("SELECT name FROM users WHERE chat_id = :chat_id");
    $stmt->execute(['chat_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
        $name = $user->name;

        // Construct the message with proper emoji
        $message = "Hi *$name* 👋\n\n$text\n-------------\n*This broadcast message is sent automatically by the bot, please don't reply to it!*";

        // Create inline keyboard for user actions
        $keyboard = [
            [
                ['text' => '🔐 Login to System', 'callback_data' => 'login_system'],
                ['text' => '📞 Contact Support', 'callback_data' => 'contact_support']
            ],
            [
                ['text' => '❓ Help & FAQ', 'callback_data' => 'help_faq'],
                ['text' => '📋 Account Status', 'callback_data' => 'account_status']
            ],
            [
                ['text' => '🔄 Reactivate Account', 'callback_data' => 'reactivate_account'],
                ['text' => '📧 Reset Password', 'callback_data' => 'reset_password']
            ]
        ];

        // Prepare data to send to Telegram API
        $data = [
            "chat_id" => $user_id,
            "text" => $message,
            "parse_mode" => "Markdown",
            "reply_markup" => json_encode([
                "inline_keyboard" => $keyboard
            ])
        ];

        // Send the request to Telegram API using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    } else {
        echo "User not found!";
        return false;
    }
}









$token = "7657734664:AAGpbkasmOSmXcldRWOeg5xpJSkkwl-cTWU";
function tgsend_sp($chat_ids, $token, $message) {
    foreach ($chat_ids as $chat_id) {
        $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($message);
        file_get_contents($url);
    }
}
?>
