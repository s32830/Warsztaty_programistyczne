<?php

function sito(int $n): array {
    $A = array_fill(0, $n + 1, true);
    
    $A[0] = $A[1] = false;
    
    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($A[$i]) {
            for ($j = $i * $i; $j <= $n; $j += $i) {
                $A[$j] = false;
            }
        }
    }
    
    $pierwsze = [];
    foreach ($A as $liczba => $czyPierwsza) {
        if ($czyPierwsza) {
            $pierwsze[] = $liczba;
        }
    }
    return $pierwsze;
}

echo "<h2>Liczby pierwsze do 100 w rzędach po 10:</h2>";
$pierwszeDo100 = sito(100);
$rzedy = array_chunk($pierwszeDo100, 10);

foreach ($rzedy as $rzad) {
    echo implode(", ", $rzad) . "<br>";
}

echo "<h2>Gęstość liczb pierwszych:</h2>";
$przedzialy = [[1, 100], [101, 200], [201, 300], [301, 400], [401, 500]];
$wszystkiePierwsze = sito(500);

foreach ($przedzialy as $p) {
    $a = $p[0];
    $b = $p[1];
    
    $licznik = 0;
    foreach ($wszystkiePierwsze as $lp) {
        if ($lp >= $a && $lp <= $b) $licznik++;
    }
    
    $srodek = ($a + $b) / 2;
    $teoretyczna = ($b - $a + 1) / log($srodek); 

    echo "Przedział [$a, $b]: Faktycznie = $licznik, Teoretycznie ≈ " . round($teoretyczna, 2) . "<br>";
}

echo "<h2>Hipoteza Goldbacha:</h2>";

$maxPary = 0;
$liczbaZMaxParami = 0;
$paryDla30 = [];

$pierwszeDo200 = sito(200);
$czyPierwsza = array_flip($pierwszeDo200);

for ($n = 4; $n <= 200; $n += 2) {
    $aktualnePary = 0;
    $tempPary = [];
    
    for ($p = 2; $p <= $n / 2; $p++) {
        if (isset($czyPierwsza[$p]) && isset($czyPierwsza[$n - $p])) {
            $aktualnePary++;
            $tempPary[] = "($p, " . ($n - $p) . ")";
        }
    }
    
    if ($aktualnePary > $maxPary) {
        $maxPary = $aktualnePary;
        $liczbaZMaxParami = $n;
    }
    
    if ($n === 30) {
        $paryDla30 = $tempPary;
    }
}

echo "Liczba z największą ilością par Goldbacha: <b>$liczbaZMaxParami</b> (ilość par: $maxPary)<br>";
echo "Pary Goldbacha dla liczby 30: " . implode(", ", $paryDla30);

?>