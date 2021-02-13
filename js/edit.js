function Edit(id){
    //sync amount
    var amount = document.getElementById("item-amount-"+id).innerHTML;
    document.getElementById("edit-amount-"+id).value =  parseInt(amount);


    document.getElementById("item-amount-"+id).style.display = "none";
    document.getElementById("item-edit-delete-"+id).style.display = "none";
    document.getElementById("item-edit-"+id).style.display = "flex";
    document.getElementById("item-save-cancel-"+id).style.display = "flex";
}

function Cancel(id){
    document.getElementById("item-amount-"+id).style.display = "flex";
    document.getElementById("item-edit-delete-"+id).style.display = "flex";
    document.getElementById("item-edit-"+id).style.display = "none";
    document.getElementById("item-save-cancel-"+id).style.display = "none";
}

function Save(id){
    var dbId = document.getElementById("edit-id-"+id).value;
    var amount = document.getElementById("edit-amount-"+id).value;

    var data = { 
        action: "edit",
        item_id: dbId,
        amount: amount 
    };

    var formData = new FormData();
    formData.append("action","edit");
    formData.append("item_id",dbId);
    formData.append("amount",amount);

    fetch('https://webik.ms.mff.cuni.cz/~cahalu/list/index.php', {
        method: 'POST',
        body: formData
    })
    .then(result => {
        Cancel(id); 
        if(result.status == 200){
            Update(id, amount);
        }
    })
    .catch(error => Cancel(id)
    );
}

function Update(id, amount){
    //item-amount- innerhtml
    //edit-amount- VALUE
    document.getElementById("item-amount-"+id).innerHTML = amount;
    document.getElementById("edit-amount-"+id).value = amount;
}