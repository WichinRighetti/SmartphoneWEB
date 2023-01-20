function getDevice () {
    console.log('Fetching Data...')
    var x = new XMLHttpRequest();
    x.open('GET', 'http://localhost/Smartphones/controllers/deviceController.php', true);
    x.send();
    x.onreadystatechange = function() {
        if (x.status == 200 && x.readyState == 4){
            showDevice(x.responseText);
        }
    }
}

function showDevice(data) {
    console.log(data);

    var container = document.getElementById("container");
    container.innerHTML = '';
    //parameter to JSON
    var JSONdata = JSON.parse(data);
    //get device information 
    var inventario = JSONdata.device;
    //read data
    for(var i = 0; i < inventario.length ; i++){
        console.log(inventario[i]);
        
        var row = document.createElement("div");
        row.className = 'row';
        var colmd112 = document.createElement("div");
        colmd112.className = "col-md-1-12";
        var card = document.createElement("div");
        card.className = "card";
        var cardBody = document.createElement("div");
        cardBody.className = "card-body";
        var cardTitle = document.createElement("h3");
        cardTitle.className = "card-title";
        cardTitle.innerHTML = inventario[i].model.Name;
        var cardMarca = document.createElement("p");
        cardMarca.className = "card-text";
        cardMarca.innerHTML = "Marca: " + inventario[i].model.Brand.Name;
        var cardMemoria = document.createElement("p");
        cardMemoria.className = "card-text";
        cardMemoria.innerHTML = "Memoria: " + inventario[i].model.Battery;
        var cardChip = document.createElement("p");
        cardChip.className = "card-text";
        cardChip.innerHTML = "Chip: " + inventario[i].model.Chip;
        var cardDisplay = document.createElement("p");
        cardDisplay.className = "card-text";
        cardDisplay.innerHTML = "Display: " + inventario[i].model.DisplaySize;
        var cardImage = document.createElement("img");
        cardImage.className = "card-text";
        cardImage.src = "assets/img/" + inventario[i].model.Image;
        var cardPrice = document.createElement("p");
        cardPrice.className = "card-text";
        cardPrice.innerHTML = "Precio: $" + inventario[i].UnitPrice +" USD";
        var cardStock = document.createElement("p");
        cardStock.className = "card-text";
        cardStock.innerHTML = "Stock: " + inventario[i].Stock;
        var BotonBuy = document.createElement("button");
        BotonBuy.className = "btn btn-primary";
        BotonBuy.type = "button";
        BotonBuy.setAttribute('onClick', 'BuyPhone('+inventario[i].Id+')');
        BotonBuy.id = 'BotonBuy';
        BotonBuy.innerHTML = "BUY NOW";
        
        container.appendChild(row);
        row.appendChild(colmd112);
        colmd112.appendChild(card);
        card.appendChild(cardBody);
        cardBody.appendChild(cardTitle);
        cardBody.appendChild(cardMarca);
        cardBody.appendChild(cardMemoria);
        cardBody.appendChild(cardChip);
        cardBody.appendChild(cardDisplay);
        cardBody.appendChild(cardImage);
        cardBody.appendChild(cardStock);
        cardBody.appendChild(cardPrice);
        cardBody.appendChild(BotonBuy);
    }
}

function BuyPhone(DeviceId){
    // create request to server 
    var x = new XMLHttpRequest();
    //crear una varabl de form data  
    var fd = new FormData();
    // create reqst 
    x.open('POST', `http://localhost/Smartphones/controllers/saleController.php`,true);
    // set  values
    fd.append('inDeviceId',DeviceId);
    fd.append('inQuantity',1);
    //send request 
    x.send(fd);
    //eventHandler
    x.onreadystatechange = function(){
    if (x.readyState == 4 && x.status == 200){
        //parse to JSON
        var JSONdata = JSON.parse(x.responseText);
        // check status
        if(JSONdata.status == 0){
            alert(JSONdata.message);
            getDevice();
        }else{
            alert(JSONdata.Message);
        }
    }
}
}
