<?php
$originCodes = $_GET['originCode'];
$destinationCodes = $_GET["destinationCode"];
$departureDate = $_GET["departureDate"];
$departureTolerance = $_GET["departureTolerance"];
$maxDaysStay = $_GET["maxDaysStay"];

$timeSinceBeggining = strtotime($departureDate);
$date = date('Y-m-d', strtotime($departureDate. ' + 2 days'));

echo $date;

$originCodesArr = json_decode($originCodes, true);
$destinationCodesArr = json_decode($destinationCodes, true);

$departures = array();
$arrivals = array();

// do{
foreach ($originCodesArr as $orig) {
    foreach ($destinationCodesArr as $dest) {
        $content = json_decode(file_get_contents("https://www.ryanair.com/api/farfnd/v4/oneWayFares/".$orig['origin_code']."/".$dest['destination_code']."/cheapestPerDay?outboundMonthOfDate=".$departureDate."&currency=EUR"), true)['outbound']['fares'];
        foreach ($content as $v) {
            if($v['departureDate'] != null){
                $v['origin_there'] = $orig['origin_name'];
                $v['destination_there'] = $dest['destination_name'];
                array_push($departures, $v);
            }
        }
    }
}
foreach ($destinationCodesArr as $dest) {
    foreach ($originCodesArr as $orig) {
        $content = json_decode(file_get_contents("https://www.ryanair.com/api/farfnd/v4/oneWayFares/".$dest['destination_code']."/".$orig['origin_code']."/cheapestPerDay?outboundMonthOfDate=".$departureDate."&currency=EUR"), true)['outbound']['fares'];
        foreach ($content as $v) {
            if($v['departureDate'] != null){
                $v['origin_return'] = $dest['destination_name'];
                $v['destination_return'] = $orig['origin_name'];
                array_push($arrivals, $v);
            }
        }
    }
}
// }while();

$table = '<table id="table">
        <thead>
        <tr>
        <th rowspan="3">No.</th>
        <th colspan="7">OUT</th>
        <th colspan="7">IN</th>
        <th rowspan="3">DAYS</th>
        <th rowspan="3">TOTAL</th>
        <th rowspan="3">BOOK</th>
        </tr>
        <tr>
        <th colspan="3">Start</th>
        <th colspan="3">Finish</th>
        <th rowspan="2">Price</th>
        <th colspan="3">Start</th>
        <th colspan="3">Finish</th>
        <th rowspan="2">Price</th>
        </tr>
        <tr>
        <th>City</th>
        <th>Day</th>
        <th>Time</th>
        <th>City</th>
        <th>Day</th>
        <th>Time</th>
        <th>City</th>
        <th>Day</th>
        <th>Time</th>
        <th>City</th>
        <th>Day</th>
        <th>Time</th>
        </tr>
        </thead>
        <tbody>';


        
$flightsCounter = 0;
foreach($departures as $flightOut){
    foreach($arrivals as $flightIn){
        $row = '<tr>';
        $row .= '<td>'.++$flightsCounter.'</td>';
        $row .= '<td>'.$flightOut['origin_there'].'</td>';
        $dayAndTimeArray = explode("T", $flightOut['departureDate']);
        $row .= '<td>'.$dayAndTimeArray[0].'</td>';
        $row .= '<td>'.$dayAndTimeArray[1].'</td>';
        $row .= '<td>'.$flightOut['destination_there'].'</td>';
        $dayAndTimeArray = explode("T", $flightOut['arrivalDate']);
        $row .= '<td>'.$dayAndTimeArray[0].'</td>';
        $row .= '<td>'.$dayAndTimeArray[1].'</td>';
        $row .= '<td>'.$flightOut['price']['value'].'</td>';
        $row .= '<td>'.$flightIn['origin_return'].'</td>';
        $dayAndTimeArray = explode("T", $flightIn['departureDate']);
        $row .= '<td>'.$dayAndTimeArray[0].'</td>';
        $row .= '<td>'.$dayAndTimeArray[1].'</td>';
        $row .= '<td>'.$flightIn['destination_return'].'</td>';
        $dayAndTimeArray = explode("T", $flightIn['arrivalDate']);
        $row .= '<td>'.$dayAndTimeArray[0].'</td>';
        $row .= '<td>'.$dayAndTimeArray[1].'</td>';
        $row .= '<td>'.$flightIn['price']['value'].'</td>';
        $row .= '<td>'."3".'</td>';
        $row .= '<td>'.intval($flightIn['price']['value']) + intval($flightIn['price']['value']).'</td>';
        $row .= '<td>BOOK</td>';
        $row .= '<tr>';
    }
    $table .= $row;
}

$table .= '</tbody></table>';

echo $table;

?>