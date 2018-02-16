<?php
$target = $_GET['user'];
$mode = $_GET['mode'];
$requestType = $_GET['requestType'];

if ($target == '' && !isset($mode) && !isset($requestType)) {
    die();
}
// str_replace()
$conn = new mysqli('localhost','root','Daohaolaji@','calendar');
$sql = "SELECT DISTINCT(User) FROM download WHERE User LIKE '%$target%'";
$sql_latest = "SELECT * FROM download ORDER BY Time DESC LIMIT 0,30";
$sql_total = "SELECT DISTINCT(User) FROM download";
if (isset($requestType)) {
    $result = $conn->query($sql_total);
    $total = $result->num_rows;
    if ($requestType == 'total') {
        $result02 = $conn->query("SELECT * FROM download");
        $totaldownloads = $result02->num_rows;
        $date_sql = "SELECT DISTINCT(DATE_FORMAT(Time,'%Y-%m-%d')) FROM download";
        $result1 = $conn->query($date_sql);

        $data = array();
        $summary = array('total' => $total, 'totaldownloads' => $totaldownloads);
        array_push($data,$summary);
        $download_people = array();
        $download_all = array();
        while ($row = $result1->fetch_row()) {
            $user_result = $conn->query("SELECT DISTINCT(User) FROM download WHERE Time < '$row[0] 23:59:59'");
            $num = $user_result->num_rows;
            // $data[$row[0]] = $num;
            $point = array('date' => $row[0], 'num' => $num);
            array_push($download_people,$point);

            $all_result = $conn->query("SELECT User FROM download WHERE Time < '$row[0] 23:59:59'");
            $all_num = $all_result->num_rows;
            // $data[$row[0]] = $num;
            $point2 = array('date' => $row[0], 'num' => $all_num);
            array_push($download_all,$point2);
        }
        array_push($data,$download_people);
        array_push($data,$download_all);
        echo json_encode($data);
    }elseif ($requestType == 'overall') {
        $result02 = $conn->query("SELECT * FROM download");
        $totaldownloads = $result02->num_rows;
        $date_sql = "SELECT DISTINCT(DATE_FORMAT(Time,'%Y-%m-%d')) FROM download";
        $result1 = $conn->query($date_sql);

        $data = array();
        $summary = array('total' => $total, 'totaldownloads' => $totaldownloads);
        array_push($data,$summary);
        $download_people = array();
        $download_all = array();
        while ($row = $result1->fetch_row()) {
            $user_result = $conn->query("SELECT DISTINCT(User) FROM download WHERE Time LIKE '$row[0]%'");
            $num = $user_result->num_rows;
            // $data[$row[0]] = $num;
            $point = array('date' => $row[0], 'num' => $num);
            array_push($download_people,$point);

            $all_result = $conn->query("SELECT User FROM download WHERE Time LIKE '$row[0]%'");
            $all_num = $all_result->num_rows;
            // $data[$row[0]] = $num;
            $point2 = array('date' => $row[0], 'num' => $all_num);
            array_push($download_all,$point2);
        }
        array_push($data,$download_people);
        array_push($data,$download_all);
        echo json_encode($data);
    }
}else{
    if (!isset($mode)) {
        $result = $conn->query($sql);
    }elseif ($mode == 'latest') {
        $result = $conn->query($sql_latest);
    }
    if ($result) {
        echo "<table style='width:100%; font-size:18px;'>";
        echo '<tr><td colspan="2" style="text-align:center;">'.$result->num_rows." found.</td></tr>";
        while ($row = $result->fetch_row()) {
            echo '<tr><td style="padding:10px;">'.$row[0].'</td><td style="padding:10px; float:right;">'.$row[2].'</td></tr>';
        }
        echo "</table>";
    }
}
?>
