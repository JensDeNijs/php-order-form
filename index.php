<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//we are going to use session variables so we need to enable sessions


session_start();
date_default_timezone_set("Europe/Brussels");


function whatIsHappening()
{
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}

$products = [
    ['name' => 'Club Ham', 'price' => 3.20],
    ['name' => 'Club Cheese', 'price' => 3],
    ['name' => 'Club Cheese & Ham', 'price' => 4],
    ['name' => 'Club Chicken', 'price' => 4],
    ['name' => 'Club Salmon', 'price' => 5]
];
//your products with their price.
if ($_SERVER['REQUEST_URI'] == "/php-order-form/?food=1") {
    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
} elseif ($_SERVER['REQUEST_URI'] == "/php-order-form/?food=0") {
    $products = [
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3],
    ];
}
if (!isset($_COOKIE["total"])) {
    $totalValue = "0";
} else {
    $totalValue = $_COOKIE["total"];
}


$errorArr = [];
$errorbox = "";
$emailErr = $streetErr = $streetNrErr = $cityErr = $zipErr = "";
$conf = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["email"])) {
        array_push($errorArr, ["Error" => "Email is required"]);
    } else {
        $email = test_input($_POST["email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errorArr, ["Error" => "Invalid email format"]);
        }
    }
    if (empty($_POST["street"])) {
        array_push($errorArr, ["Error" => "Street is required"]);
    }
    if (empty($_POST["streetnumber"])) {
        array_push($errorArr, ["Error" => "Streetnumber is required"]);
    } else {
        $streetnumber = test_input($_POST["streetnumber"]);
        if (!preg_match('/^[0-9]*$/', $streetnumber)) {
            array_push($errorArr, ["Error" => "Streetnumber can only contain numbers"]);
        }
    }
    if (empty($_POST["city"])) {
        array_push($errorArr, ["Error" => "city is required"]);
    }
    if (empty($_POST["zipcode"])) {
        array_push($errorArr, ["Error" => "Zipcode is required"]);
    } else {
        $zipcode = test_input($_POST["zipcode"]);
        if (!preg_match('/^[0-9]*$/', $zipcode)) {
            array_push($errorArr, ["Error" => "Zipcode can only contain numbers"]);
        }
    }
    $subtotal = "0";
    if (!empty($_POST['products'])) {
        foreach ($_POST['products'] as $i => $product) {
            $subtotal += $products[$i]['price'];
        }
    }


    $_SESSION["email"] = $_POST["email"];
    $_SESSION["street"] = $_POST["street"];
    $_SESSION["streetNr"] = $_POST["streetnumber"];
    $_SESSION["city"] = $_POST["city"];
    $_SESSION["zip"] = $_POST["zipcode"];

    if ($subtotal == "0") {
        array_push($errorArr, ["Error" => "Select at least 1 option"]);

    } elseif (empty($errorArr)) {
        $conf = "Your order has been send.";


        if (isset($_POST['express_delivery'])) {
            $conf = $conf . "<br> The delivery will arrive at " . date("H:i", strtotime("+45 minutes"));
        } else {
            $conf = $conf . "<br> The delivery will arrive at " . date("H:i", strtotime("+2 hours"));
        }


        if (!empty($_POST['products'])) {
            foreach ($_POST['products'] as $i => $product) {
                $totalValue += $products[$i]['price'];
            }
        }

        setcookie('total', strval($totalValue));

    }


}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

require 'form-view.php';