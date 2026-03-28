<?php

$transakcje = [
    ["id"=>1,  "data"=>"2024-01-15", "kategoria"=>"Elektronika", "kwota"=>1200.00],
    ["id"=>2,  "data"=>"2024-01-22", "kategoria"=>"Dom",         "kwota"=>350.00],
    ["id"=>3,  "data"=>"2024-02-03", "kategoria"=>"Elektronika", "kwota"=>800.00],
    ["id"=>4,  "data"=>"2024-02-14", "kategoria"=>"Odzież",      "kwota"=>250.00],
    ["id"=>5,  "data"=>"2024-02-28", "kategoria"=>"Dom",         "kwota"=>420.00],
    ["id"=>6,  "data"=>"2024-03-05", "kategoria"=>"Elektronika", "kwota"=>1500.00],
    ["id"=>7,  "data"=>"2024-03-12", "kategoria"=>"Odzież",      "kwota"=>180.00],
    ["id"=>8,  "data"=>"2024-03-19", "kategoria"=>"Dom",         "kwota"=>290.00],
    ["id"=>9,  "data"=>"2024-01-08", "kategoria"=>"Odzież",      "kwota"=>310.00],
    ["id"=>10, "data"=>"2024-01-30", "kategoria"=>"Elektronika", "kwota"=>950.00],
    ["id"=>11, "data"=>"2024-02-10", "kategoria"=>"Dom",         "kwota"=>600.00],
    ["id"=>12, "data"=>"2024-03-25", "kategoria"=>"Odzież",      "kwota"=>430.00],
    ["id"=>13, "data"=>"2024-01-18", "kategoria"=>"Elektronika", "kwota"=>2100.00],
    ["id"=>14, "data"=>"2024-02-22", "kategoria"=>"Dom",         "kwota"=>175.00],
    ["id"=>15, "data"=>"2024-03-08", "kategoria"=>"Elektronika", "kwota"=>670.00],
    ["id"=>16, "data"=>"2024-01-25", "kategoria"=>"Odzież",      "kwota"=>520.00],
    ["id"=>17, "data"=>"2024-02-17", "kategoria"=>"Elektronika", "kwota"=>1350.00],
    ["id"=>18, "data"=>"2024-03-14", "kategoria"=>"Dom",         "kwota"=>480.00],
    ["id"=>19, "data"=>"2024-01-12", "kategoria"=>"Dom",         "kwota"=>230.00],
    ["id"=>20, "data"=>"2024-02-05", "kategoria"=>"Odzież",      "kwota"=>390.00],
];

$pivot = [];
$dane_kategorii = [];

foreach ($transakcje as $t) {
    $kat = $t['kategoria'];
    $miesiac = substr($t['data'], 0, 7);
    $kwota = $t['kwota'];

    if (!isset($pivot[$kat])) {
        $pivot[$kat] = ['2024-01' => 0, '2024-02' => 0, '2024-03' => 0];
    }
    $pivot[$kat][$miesiac] += $kwota;

    if (!isset($dane_kategorii[$kat])) {
        $dane_kategorii[$kat] = [];
    }
    $dane_kategorii[$kat][] = $kwota;
}

ksort($pivot);
ksort($dane_kategorii);

echo "<pre style='background:#1e1e24; color:#a1a1aa; padding:15px; border-radius:5px;'>\n";

printf("%-14s | %8s | %8s | %8s\n", "Kategoria", "Styczeń", "Luty", "Marzec");
echo str_repeat("-", 47) . "\n\n";

foreach ($pivot as $kat => $miesiace) {
    $m1 = number_format($miesiace['2024-01'], 2, '.', '');
    $m2 = number_format($miesiace['2024-02'], 2, '.', '');
    $m3 = number_format($miesiace['2024-03'], 2, '.', '');
    
    printf("%-14s | %8s | %8s | %8s\n", $kat, $m1, $m2, $m3);
}

echo "\nOdchylenia standardowe (σ):\n";

$max_sigma = -1;
$max_kategoria = "";

foreach ($dane_kategorii as $kat => $kwoty) {
    $n = count($kwoty);
    
    $suma = 0;
    foreach ($kwoty as $k) {
        $suma += $k;
    }
    $avg = $suma / $n;

    $suma_roznic = 0;
    foreach ($kwoty as $k) {
        $suma_roznic += ($k - $avg) * ($k - $avg);
    }
    
    $sigma = sqrt($suma_roznic / $n);

    if ($sigma > $max_sigma) {
        $max_sigma = $sigma;
        $max_kategoria = $kat;
    }

    $sigma_format = number_format($sigma, 2, '.', '');
    $avg_format = number_format($avg, 2, '.', '');
    
    printf("%-14s : σ=%s (n=%d, avg=%s zł)\n", $kat, $sigma_format, $n, $avg_format);
}

$max_sigma_format = number_format($max_sigma, 2, '.', '');
echo "\nKategoria o największej zmienności: $max_kategoria (σ=$max_sigma_format)\n";

echo "</pre>";

?>