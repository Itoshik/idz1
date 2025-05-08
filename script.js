function fetchCrypto() {
    const symbol = document.getElementById('symbolInput').value.trim().toUpperCase();
    if (!symbol) {
        alert('Введіть символ криптовалюти!');
        return;
    }
    
    fetch(`server.php?symbol=${symbol}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('result').style.display = 'block';
            document.getElementById('logo').src = data.logo;
            document.getElementById('name').innerText = `${data.name} (${data.symbol})`;
            document.getElementById('price').innerText = data.price.toFixed(2);
            document.getElementById('change').innerText = data.percent_change_24h.toFixed(2);
            document.getElementById('website').href = data.website;
            document.getElementById('website').innerText = 'Перейти на сайт';
            document.getElementById('cmcPage').href = data.coinmarketcap_url;
            document.getElementById('cmcPage').innerText = 'Перейти на сторінку CoinMarketCap';
            document.getElementById('description').innerText = data.description || 'Немає опису.';
        })
        .catch(error => {
            console.error('Помилка:', error);
            alert('Сталася помилка при запиті до сервера.');
        });
}
