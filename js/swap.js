function Swap(item_id, direction, el){
    var rows = document.getElementById("table").rows;
    var thisID = item_id;
    var thisRow = document.getElementById("item-"+thisID);
    if(direction == 1){
        var otherEl = thisRow.nextElementSibling;
    }
    if(direction == -1){
        var otherEl = thisRow.previousElementSibling;
    }
    if(otherEl == null){
        //out of table
        return;
    }
    var otherID = otherEl.id.substr(5);

    var formData = new FormData();
    formData.append("action","swap");
    formData.append("item_id_one",thisID);
    formData.append("item_id_two",otherID);

    fetch('https://webik.ms.mff.cuni.cz/~cahalu/list/index.php', {
        method: 'POST',
        body: formData
    })
    .then(result => {
        var row1 = document.getElementById("item-"+thisID);
        var row2 = document.getElementById("item-"+otherID);
        if(direction == 1){
            row2.parentNode.insertBefore(row2, row1);
        }
        if(direction == -1){
            row2.parentNode.insertBefore(row1, row2);
        }
        
        //switch disability
        var buttonOneUp = row1.getElementsByClassName("button-up")[0];
        var buttonOneDown = row1.getElementsByClassName("button-down")[0];
        var buttonTwoUp = row2.getElementsByClassName("button-up")[0];
        var buttonTwoDown = row2.getElementsByClassName("button-down")[0];
        var tempUp = buttonOneUp.disabled;
        var tempDown = buttonOneDown.disabled;
        buttonOneUp.disabled =   buttonTwoUp.disabled;
        buttonOneDown.disabled = buttonTwoDown.disabled;
        buttonTwoUp.disabled = tempUp;
        buttonTwoDown.disabled = tempDown;
    })
    .catch(error => console.log("error with swap"));


    
}