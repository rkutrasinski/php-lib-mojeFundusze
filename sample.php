<?php

include 'mojeFunduszeClient.php';

$mf = new mojeFunduszeClient();
//$mf->updateAllPrices();
$mf->updateIndex();
$mf->updateProfiles();
$mf->updateCurrentPrices();
$mf->updateUmbrella();
$mf->updateDocuments();
