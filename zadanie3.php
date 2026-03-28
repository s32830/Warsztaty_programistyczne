<?php
$dokumenty = [
    0 => "PHP jest jezykiem skryptowym uzywanym do tworzenia stron internetowych",
    1 => "Tablice w PHP moga byc indeksowane lub asocjacyjne i bardzo przydatne",
    2 => "Funkcje array_map i array_filter ulatwiaja przetwarzanie tablic w PHP",
    3 => "PHP obsluguje tablice wielowymiarowe i zagniezdzone struktury danych",
    4 => "Serwer Apache wspolpracuje z PHP do obslugi zadan HTTP i polaczen",
    5 => "Bazy danych MySQL sa czesto uzywane razem z PHP do przechowywania",
    6 => "Funkcja usort sortuje tablice w PHP wedlug roznych kryteriow i warunkow",
    7 => "JavaScript i PHP razem tworza dynamiczne aplikacje internetowe i serwisy",
    8 => "PHP posiada wbudowane funkcje do pracy z plikami tablicami i bazami",
    9 => "Bezpieczenstwo aplikacji PHP wymaga walidacji danych wejsciowych i filtrow",
];

$stop_words = ['i', 'w', 'na', 'do', 'z', 'sa', 'lub', 'byc', 'moze', 'jest', 'sie'];
$indeks = [];
$licznik_slow = [];

foreach ($dokumenty as $id => $tekst) {
    $tekst = strtolower($tekst);
    
    $tekst = str_replace(['.', ',', '!', '?'], '', $tekst);
    
    $slowa = explode(' ', $tekst);
    
    foreach ($slowa as $slowo) {
        if (strlen($slowo) >= 3 && !in_array($slowo, $stop_words)) {
            
            if (!isset($indeks[$slowo][$id])) {
                $indeks[$slowo][$id] = 1;
            } else {
                $indeks[$slowo][$id]++;
            }

            if (!isset($licznik_slow[$slowo])) {
                $licznik_slow[$slowo] = 1;
            } else {
                $licznik_slow[$slowo]++;
            }
        }
    }
}

echo "<h3>Top 5 najczęstszych słów:</h3>";
arsort($licznik_slow); 

$i = 0;
foreach ($licznik_slow as $slowo => $ile) {
    if ($i >= 5) break;
    echo "$slowo - występuje $ile razy<br>";
    $i++;
}

echo "<h3>Wyniki dla AND (php, tablice):</h3>";
$wyniki_and = [];

foreach ($dokumenty as $id => $tekst) {
    $ma_php = isset($indeks['php'][$id]);
    $ma_tablice = isset($indeks['tablice'][$id]);
    
    if ($ma_php && $ma_tablice) {
        $punkty = $indeks['php'][$id] + $indeks['tablice'][$id];
        $wyniki_and[$id] = $punkty;
    }
}

arsort($wyniki_and);
foreach ($wyniki_and as $id => $punkty) {
    echo "Dokument ID: $id | Punkty: $punkty <br>";
}

echo "<h3>Wyniki dla OR (mysql, javascript):</h3>";
$wyniki_or = [];

foreach ($dokumenty as $id => $tekst) {
    $ma_mysql = isset($indeks['mysql'][$id]);
    $ma_js = isset($indeks['javascript'][$id]);
    
    if ($ma_mysql || $ma_js) {
        $punkty = 0;
        if ($ma_mysql) $punkty += $indeks['mysql'][$id];
        if ($ma_js) $punkty += $indeks['javascript'][$id];
        
        $wyniki_or[$id] = $punkty;
    }
}

arsort($wyniki_or);
foreach ($wyniki_or as $id => $punkty) {
    echo "Dokument ID: $id | Punkty: $punkty <br>";
}
?>