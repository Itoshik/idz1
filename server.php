<?php
$apiKey = 'eea078cd-0731-49de-b914-3ec421ae9583'; // API ключ
$baseUrl = 'https://pro-api.coinmarketcap.com/v1';

if (isset($_GET['symbol'])) {
    $symbol = strtoupper($_GET['symbol']);
    
    // Запит до CoinMarketCap для отримання інформації про криптовалюту
    $url = "$baseUrl/cryptocurrency/info?symbol=$symbol";
    $headers = [
        "X-CMC_PRO_API_KEY: $apiKey",
        "Accept: application/json"
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    curl_close($ch);

    // Перевірка на помилки
    if ($response === false) {
        echo json_encode(["error" => "Не вдалося отримати дані від CoinMarketCap"]);
        exit;
    }

    $data = json_decode($response, true);

    // Якщо криптовалюта не знайдена
    if (!isset($data['data'][$symbol])) {
        echo json_encode(["error" => "Криптовалюту не знайдено"]);
        exit;
    }

    $cryptoData = $data['data'][$symbol];

    // Додатковий запит для отримання курсу та зміни за 24 години
    $url = "$baseUrl/cryptocurrency/quotes/latest?symbol=$symbol";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    curl_close($ch);

    // Перевірка на помилки
    if ($response === false) {
        echo json_encode(["error" => "Не вдалося отримати дані про курс"]);
        exit;
    }

    $priceData = json_decode($response, true);
    
    if (!isset($priceData['data'][$symbol]['quote']['USD'])) {
        echo json_encode(["error" => "Не вдалося отримати курс для криптовалюти"]);
        exit;
    }

    $priceInfo = $priceData['data'][$symbol]['quote']['USD'];
    
    // Формуємо відповідь
    $response = [
        'name' => $cryptoData['name'],
        'symbol' => $symbol,
        'website' => $cryptoData['urls']['website'][0] ?? '',
        'logo' => $cryptoData['logo'],
        'description' => $cryptoData['description'] ?? '',
        'price' => $priceInfo['price'],
        'percent_change_24h' => $priceInfo['percent_change_24h'],
        'coinmarketcap_url' => "https://coinmarketcap.com/currencies/" . $cryptoData['slug'] . "/"
    ];

    echo json_encode($response);
} else {
    echo json_encode(["error" => "Не вказано символ криптовалюти."]);
}