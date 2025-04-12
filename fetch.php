<?php
session_start();
$numero=$_POST['numero'];
// $numero=3110000000;

require 'db.php';
$records_3 = $conn->prepare('SELECT phone, name, nivel_1 FROM usuarios WHERE nivel_1=:numero OR nivel_2=:numero OR nivel_3=:numero OR nivel_4=:numero OR nivel_5=:numero OR nivel_6=:numero OR nivel_7=:numero OR nivel_8=:numero OR nivel_9=:numero');
$records_3->bindParam(':numero', $numero);
$records_3->execute();

while($row =$records_3->fetch(PDO::FETCH_ASSOC))
{
 $sub_data["phone"] = $row["phone"];
 $sub_data["name"] = $row["name"];
 $sub_data["text"] = $row["name"];
 $sub_data["nivel_1"] = $row["nivel_1"];
 $data[] = $sub_data;
}
foreach($data as $key => &$value)
{
 $output[$value["phone"]] = &$value;
}
foreach($data as $key => &$value)
{
 if($value["nivel_1"] && isset($output[$value["nivel_1"]]))
 {
  $output[$value["nivel_1"]]["nodes"][] = &$value;
 }
}
foreach($data as $key => &$value)
{
 if($value["nivel_1"] && isset($output[$value["nivel_1"]]))
 {
  unset($data[$key]);
 }
}

echo json_encode($data);
// echo '<pre>';
// print_r($data);
// echo '</pre>';
