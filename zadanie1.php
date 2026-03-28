<?php
$zestawy_danych = [
    [5, 3, 8, 1, 9, 2],
    [38, 27, 43, 3, 9, 82, 10, 15],
    [64, 25, 12, 22, 11, 90, 3, 47, 71, 38, 55, 8],
    [25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
];

function mergeSort($arr, &$comparisons) {
    $n = count($arr);
    
    if ($n <= 1) {
        return $arr;
    }

    $mid = (int)($n / 2);
    $left = array_slice($arr, 0, $mid);
    $right = array_slice($arr, $mid);

    $left = mergeSort($left, $comparisons);
    $right = mergeSort($right, $comparisons);

    return merge($left, $right, $comparisons);
}

function merge($left, $right, &$comparisons) {
    $result = [];
    

    while (count($left) > 0 && count($right) > 0) {
        $comparisons++;
        if ($left[0] <= $right[0]) {
            $result[] = array_shift($left);
        } else {
            $result[] = array_shift($right);
        }
    }
    
    return array_merge($result, $left, $right);
}

foreach ($zestawy_danych as $tablica) {
    $n = count($tablica);
    $comparisons = 0; 
    
    echo "<h3>Test dla tablicy (n=$n)</h3>";
    echo "Wejście: " . implode(", ", $tablica) . "<br>";

    $posortowana = mergeSort($tablica, $comparisons);
	sort($tablica);

    $k = $comparisons / ($n * log($n, 2));

    echo "Wyjście: " . implode(", ", $posortowana) . "<br>";
    echo "Liczba porównań: <b>$comparisons</b><br>";
    echo "Współczynnik K: <b>" . round($k, 4) . "</b><br>";
	echo "Wyjście posortowane: " . implode(", ", $tablica) . "<br>";
    echo "<hr>";
}

?>