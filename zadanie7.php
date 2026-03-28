<?php

$oceny = [
    "Anna"    => [5, 4, null, 2, null, 3, 4, 5],
    "Bartek"  => [4, 5, 3, null, 2, 4, null, 4],
    "Celina"  => [5, 3, null, 3, null, 4, 5, null],
    "Dawid"   => [2, null, 4, 5, 3, null, 2, 3],
    "Ewa"     => [null, 4, 3, null, 5, 3, 4, 2],
    "Filip"   => [3, 5, 4, 2, null, 5, null, 4],
    "Grażyna" => [5, null, 2, 4, 3, 2, 5, null],
];
$produkty = ["Laptop", "Monitor", "Klawiatura", "Mysz", "Słuchawki", "Kamera", "Tablet", "Głośnik"];

function korelacja_pearsona($ocenyA, $ocenyB) {
    $wspolneA = [];
    $wspolneB = [];
    
    for ($i = 0; $i < 8; $i++) {
        if ($ocenyA[$i] !== null && $ocenyB[$i] !== null) {
            $wspolneA[] = $ocenyA[$i];
            $wspolneB[] = $ocenyB[$i];
        }
    }
    
    $n = count($wspolneA);
    if ($n < 2) {
        return 0;
    }
    
    $sredniaA = array_sum($wspolneA) / $n;
    $sredniaB = array_sum($wspolneB) / $n;
    
    $licznik = 0;
    $mianownikA = 0;
    $mianownikB = 0;
    
    for ($i = 0; $i < $n; $i++) {
        $roznicaA = $wspolneA[$i] - $sredniaA;
        $roznicaB = $wspolneB[$i] - $sredniaB;
        
        $licznik += $roznicaA * $roznicaB;
        $mianownikA += pow($roznicaA, 2);
        $mianownikB += pow($roznicaB, 2);
    }
    
    if ($mianownikA == 0 || $mianownikB == 0) {
        return 0;
    }
    
    return $licznik / sqrt($mianownikA * $mianownikB);
}

$podobienswa = [];
foreach ($oceny as $osoba => $oc) {
    if ($osoba === "Anna") continue;
    $podobienswa[$osoba] = korelacja_pearsona($oceny["Anna"], $oc);
}

arsort($podobienswa);

echo "<pre style='background:#1e1e24; color:#a1a1aa; padding:15px; border-radius:5px;'>\n";

echo "Podobieństwo Pearsona dla <span style='color:#b48ead;'>Anny</span>:\n";
foreach ($podobienswa as $osoba => $wynik) {
    printf("  <span style='color:#81a1c1;'>%-7s</span>: %7.4f\n", $osoba, $wynik);
}
echo "\n";

$k = 3;
$sasiedzi = array_slice($podobienswa, 0, $k, true);

$sasiedzi_str = [];
foreach ($sasiedzi as $osoba => $wynik) {
    $sasiedzi_str[] = "<span style='color:#81a1c1;'>$osoba</span>(" . number_format($wynik, 4) . ")";
}
echo "k=3 sąsiedzi <span style='color:#b48ead;'>Anny</span>: " . implode(", ", $sasiedzi_str) . "\n\n";

$rekomendacje = [];
for ($i = 0; $i < count($produkty); $i++) {
    if ($oceny["Anna"][$i] === null) {
        $licznik = 0;
        $mianownik = 0;
        
        foreach ($sasiedzi as $sasiad => $sim) {
            $ocena_sasiada = $oceny[$sasiad][$i];
            if ($ocena_sasiada !== null) {
                $licznik += $sim * $ocena_sasiada;
                $mianownik += abs($sim);
            }
        }
        
        if ($mianownik > 0) {
            $przewidywana = $licznik / $mianownik;
            $rekomendacje[$produkty[$i]] = $przewidywana;
        }
    }
}

arsort($rekomendacje);

echo "Rekomendacje dla <span style='color:#b48ead;'>Anny</span> (produkty nieocenione):\n";
$lp = 1;
foreach ($rekomendacje as $produkt => $ocena) {
    printf("  %d. %-12s — przewidywana <span style='color:#81a1c1;'>ocena</span>: %.2f\n", $lp, $produkt, $ocena);
    $lp++;
}
echo "\n";

echo "<span style='color:#b48ead;'>Zimny start</span> (Hania, <span style='color:#81a1c1;'>1 ocena</span>):\n";
echo "  Za mało wspólnych ocen z innymi użytkownikami — brak wiarygodnych korelacji.\n";
echo "  <span style='color:#b48ead;'>Strategia</span>: rekomenduj najpopularniejsze <span style='color:#81a1c1;'>produkty</span> (najwyższa średnia ocen wśród wszystkich).\n";

echo "</pre>";

?>