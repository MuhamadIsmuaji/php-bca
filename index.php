<?php

require_once 'Bca.php';

// Change this path to your desired API Services Path
$path = '/banking/v2/corporates/BCAAPI2016/accounts/0201245680';
$method = 'GET';
$data = [];

$Bca = new Bca();
// $Bca->getToken();
// $Bca->getSignature($path, $method, $data);
$Bca->BalanceInformation();
