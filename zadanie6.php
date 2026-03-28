<?php

$zadania = [
    ["id"=>1,  "nazwa"=>"T01", "start"=>480, "koniec"=>600],
    ["id"=>2,  "nazwa"=>"T02", "start"=>510, "koniec"=>720],
    ["id"=>3,  "nazwa"=>"T03", "start"=>540, "koniec"=>660],
    ["id"=>4,  "nazwa"=>"T04", "start"=>600, "koniec"=>690],
    ["id"=>5,  "nazwa"=>"T05", "start"=>660, "koniec"=>780],
    ["id"=>6,  "nazwa"=>"T06", "start"=>690, "koniec"=>840],
    ["id"=>7,  "nazwa"=>"T07", "start"=>720, "koniec"=>810],
    ["id"=>8,  "nazwa"=>"T08", "start"=>780, "koniec"=>900],
    ["id"=>9,  "nazwa"=>"T09", "start"=>840, "koniec"=>960],
    ["id"=>10, "nazwa"=>"T10", "start"=>480, "koniec"=>540],
    ["id"=>11, "nazwa"=>"T11", "start"=>570, "koniec"=>630],
    ["id"=>12, "nazwa"=>"T12", "start"=>750, "koniec"=>870],
    ["id"=>13, "nazwa"=>"T13", "start"=>900, "koniec"=>990],
    ["id"=>14, "nazwa"=>"T14", "start"=>495, "koniec"=>555],
    ["id"=>15, "nazwa"=>"T15", "start"=>870, "koniec"=>930]
];

function minutyNaCzas(int $m): string {
    $godziny = floor($m / 60);
    $minuty = $m % 60;
    return $godziny . ":" . str_pad($minuty, 2, "0", STR_PAD_LEFT);
}

$zadania_czesc1 = $zadania;
usort($zadania_czesc1, function($a, $b) {
    return $a['koniec'] - $b['koniec'];
});

$wybrane = [];
$ostatni_koniec = 0;

foreach ($zadania_czesc1 as $z) {
    if ($z['start'] >= $ostatni_koniec) {
        $wybrane[] = $z;
        $ostatni_koniec = $z['koniec'];
    }
}

$najbardziej_konfliktowe = "";
$max_kolizji = -1;

foreach ($zadania as $z1) {
    $kolizje = 0;
    foreach ($zadania as $z2) {
        if ($z1['id'] != $z2['id']) {
            $max_start = max($z1['start'], $z2['start']);
            $min_koniec = min($z1['koniec'], $z2['koniec']);
            if ($max_start < $min_koniec) {
                $kolizje++;
            }
        }
    }
    if ($kolizje > $max_kolizji) {
        $max_kolizji = $kolizje;
        $najbardziej_konfliktowe = $z1['nazwa'];
    }
}

$zadania_czesc3 = $zadania;
usort($zadania_czesc3, function($a, $b) {
    return $a['start'] - $b['start'];
});

$sale = [];

foreach ($zadania_czesc3 as $z) {
    $przypisano = false;
    for ($i = 0; $i < count($sale); $i++) {
        $ostatnie_zadanie = $sale[$i][count($sale[$i]) - 1];
        if ($ostatnie_zadanie['koniec'] <= $z['start']) {
            $sale[$i][] = $z;
            $przypisano = true;
            break;
        }
    }
    if (!$przypisano) {
        $sale[] = [$z];
    }
}

echo "<pre style='background:#1e1e24; color:#a1a1aa; padding:15px; border-radius:5px; font-family: monospace;'>\n";

echo "Algorytm <span style='color:#b48ead;'>zachłanny</span> (jedna sala):\n";
$nazwy_wybranych = [];
$kolejnosc_decyzji = [];
foreach ($wybrane as $w) {
    $nazwy_wybranych[] = $w['nazwa'];
    $kolejnosc_decyzji[] = $w['nazwa'] . "(" . minutyNaCzas($w['start']) . "-" . minutyNaCzas($w['koniec']) . ")";
}
echo "  Wybrane <span style='color:#81a1c1;'>zadania</span> (" . count($wybrane) . "): " . implode(", ", $nazwy_wybranych) . "\n";
echo "  Kolejność <span style='color:#b48ead;'>decyzji</span>: " . implode(" -> ", $kolejnosc_decyzji) . "\n\n";

echo "<span style='color:#b48ead;'>Konflikty</span>:\n";
echo "  Najbardziej <span style='color:#b48ead;'>konfliktowe</span>: $najbardziej_konfliktowe ($max_kolizji kolizji z innymi zadaniami)\n\n";

echo "Minimalna liczba <span style='color:#81a1c1;'>sal</span>: " . count($sale) . "\n";
for ($i = 0; $i < count($sale); $i++) {
    $nr_sali = $i + 1;
    $zadania_w_sali = [];
    foreach ($sale[$i] as $z) {
        $zadania_w_sali[] = "<span style='color:#b48ead;'>" . $z['nazwa'] . "</span>(" . minutyNaCzas($z['start']) . "-" . minutyNaCzas($z['koniec']) . ")";
    }
    echo "  <span style='color:#81a1c1;'>Sala $nr_sali</span>: " . implode(", ", $zadania_w_sali) . "\n";
}

echo "</pre>";
?>