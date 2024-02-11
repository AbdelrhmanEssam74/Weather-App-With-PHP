<?php
define('API_key', "cbe439ed5d7e9fd4dee89b0183fa437d");
$message = "";
$city_name = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['submit'])) {
        if (empty($_POST['city'])) {
            echo "<script> alert('Invalid Input')</script>";
        } else {
            $city_name = ucfirst(strtolower(trim($_POST['city'])));
            $url = "https://api.openweathermap.org/data/2.5/weather?q={$city_name}&appid=" . API_key;
            $data = file_get_contents($url);
            $weather_data = json_decode($data, true);
            $request_code = $weather_data['cod'];
            if ($request_code != "200") {
                header("Location:" . $_SERVER["REQUEST_URI"]);
            } else {
                $offsetSeconds = $weather_data['timezone'];
                $timezoneName = timezone_name_from_abbr('', $offsetSeconds, false);
                date_default_timezone_set($timezoneName);
                $weather_icon = $weather_data['weather'][0]['icon'];
                $iconUrl = "http://openweathermap.org/img/wn/$weather_icon.png";
                $timestamp_sunrise = $weather_data['sys']['sunrise'];
                $timestamp_sunset = $weather_data['sys']['sunset'];
                $formatted_date_rise = date('h:i', $timestamp_sunrise);
                $formatted_date_set = date('h:i', $timestamp_sunset);
                $weather_arr = [];
                array_push($weather_arr, [
                    'description' => $weather_data['weather'][0]['main'],
                    'temp' => round((float)$weather_data['main']['temp'] - 273.15),
                    'temp_feel_like' => round((float)$weather_data['main']['feels_like'] - 273.15),
                    'country' => $weather_data['sys']['country'],
                    "icon" => $iconUrl,
                    "sunrise" => $formatted_date_rise,
                    "sunset" =>  $formatted_date_set
                ]);
            }
        }

        // echo "<pre>";
        // print_r($weather_data);
        // echo "</pre>";

        // $original = new DateTime('now', new DateTimeZone('UTC'));
        // $modified = $original->setTimezone(new DateTimeZone($timezoneName));



    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="main.">
    <title>Weather App</title>
</head>

<body>

    <div class="conatiner">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h3 class="text-center head-margin">What's the weather today??</h3>
                <form class="mb-4 card p-2 margin" method="POST" action="index.php">
                    <div class="input-group">
                        <input type="text" name="city" class="form-control" placeholder="Enter city name e.g Cairo, Berlin, Tokyo">
                        <div class="input-group-append">
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        if (isset($request_code)  && $request_code == "200") :
            echo "
            <div class='weather-box'>
            <div class='weather-icon'>";
        ?>
            <img src='<?php echo $weather_arr[0]['icon'] ?>' alt=''>
    </div>
    <div class='temperature'><?php echo $weather_arr[0]['temp']  . " °C , Feels Like " . $weather_arr[0]['temp_feel_like'] . " °C" ?></div>
    <div class='description'><?php echo $weather_arr[0]['description'] . " In " .  $city_name . ", " . $weather_arr[0]['country'] ?> </div>
    <div class='description'><?php echo "Sun will rise at " . $weather_arr[0]['sunrise'] . "AM and set at " . $weather_arr[0]['sunset'] . "PM" ?> .</div>
<?php
        endif;
?>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

</body>

</html>