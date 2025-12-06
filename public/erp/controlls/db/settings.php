
<?php
try{
if(isset($_POST['new'])){
    $time = $_POST['time'];
    $days = $_POST['days'];
    $users_up_get = $conn->prepare("UPDATE `settings` SET `time` = :time , `days` = :days WHERE `id` = 1 ");
    $users_up_get->execute([
    'time' => $time,
    'days' => $days,
    ]);
    echo "<meta http-equiv='refresh' content='0'>";
}
}catch(Exception $e){
echo  $e->getMessage();
}
?>
