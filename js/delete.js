function Delete(id){
    fetch('https://webik.ms.mff.cuni.cz/~cahalu/list/index.php?id=' + id, {
        method: 'DELETE',
    })
    .then(result => {
        var deletedRow = document.getElementById("item-"+id);
        deletedRow.remove();
    })
    .catch(error => {
        //display connection error, unable to delete
        var errorDisplayElement = document.getElementById("error-display-element");

        var div = document.createElement("div");
        var strong = document.createElement("strong");
        var strongText = document.createTextNode("Network error!");
        var text = document.createTextNode(" Unable to connect to database.");

        strong.append(strongText);
        div.appendChild(strong);
        div.appendChild(text);
        div.className = "alert alert-danger";
        errorDisplayElement.append(div);
    });
}