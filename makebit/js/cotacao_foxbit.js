        var BlinkTradeRest = require("blinktrade").BlinkTradeRest;
        var blinktrade = new BlinkTradeRest({
        prod: false,
        key: "8DtESTWG80cYa162UYqt10nx20ba0N",
        secret: "i9VA6ru0PRlyIUI",
        currency: "BRL",
        });

        blinktrade.ticker().then(function(ticker) {
          console.log(ticker);
        });
