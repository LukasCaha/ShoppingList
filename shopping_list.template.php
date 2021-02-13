<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shopping List</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>
    <div class="error-display" id="error-display-element">

<?php if(isset($_GET["success"])){ ?>
        <div class="alert alert-success">
            <strong>Item added!</strong> 
        </div>
<?php }?>

<?php if(isset($_GET["errorMessage"])){ ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> <?=htmlspecialchars($_GET["errorMessage"])?>
        </div>
<?php }?>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <h1 class="mt-5 col-8">Shopping List</h1>
            <div class="list mt-4 col-8">
                <table class="table" id="table">
                    <colgroup>
                        <col class="stretch">
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                        <tr id="gray-border-bottom">
                            <th>Item</th>
                            <th>Amount</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
<?php 
    $lineCounter = 0;
    foreach($tableData as $item){ 
    $lineCounter++;    
?>
                        <tr id="item-<?=htmlspecialchars($item['item_id'])?>">
                            <td> <?=htmlspecialchars($item["name"])?> </td>
                            <td>
                                <!--Amount display-->
                                <div id="item-amount-<?=$lineCounter?>" class="toggle-default-on"> <?=htmlspecialchars($item["amount"])?> </div>
                                <!--Amount edit form-->
                                <div class="toggle-default-off" id="item-edit-<?=$lineCounter?>">
                                    <form action="#">
                                        <input type="hidden" id='edit-id-<?=$lineCounter?>' value='<?=htmlspecialchars($item["item_id"])?>'>
                                        <input type="number" id='edit-amount-<?=$lineCounter?>' value='<?=htmlspecialchars($item["amount"])?>' min="1" class="item-amount-form">
                                    </form>
                                </div>
                            </td>
                            <td>
                                <!--Change order UP-->
                                <button class="btn btn-sm btn-primary in-table button-up" <?php if($lineCounter === 1) echo "disabled";?> onclick="Swap(<?=htmlspecialchars($item["item_id"])?>,-1, this)"> &uarr; </button> 
                                <!--Change order DOWN-->
                                <button class="btn btn-sm btn-primary in-table button-down" <?php if($lineCounter === count($tableData)) echo "disabled";?> onclick="Swap(<?=htmlspecialchars($item["item_id"])?>,1, this)"> &darr; </button> 
                            </td>
                            <td> 
                                <div class="stuffing"></div>
                                <div id="item-edit-delete-<?=$lineCounter?>" class="button-flex-wraper toggle-default-on">
                                    <!--Edit button-->
                                    <button class="btn btn-warning btn-sm" onclick='Edit(<?=$lineCounter?>)'>Edit</button>
                                    <!--Delete button-->
                                    <button class="btn btn-danger btn-sm" onclick="Delete(<?=htmlspecialchars($item['id'])?>)">Delete</button>
                                </div>
                                <div id="item-save-cancel-<?=$lineCounter?>" class="button-flex-wraper toggle-default-off">
                                    <!--Save button-->
                                    <button class="btn btn-success btn-sm" onclick='Save(<?=$lineCounter?>)'>Save</button>
                                    <!--Cancel edit button-->
                                    <button class="btn btn-danger btn-sm" onclick='Cancel(<?=$lineCounter?>)'>Cancel</button>
                                </div>
                            </td>
                        </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-center">
            <h4 class="mt-5 col-7">Add Item</h4>
            <div class="add mt-4 mb-5 col-7">
                <form action="." method="POST">
                    <input type="hidden" name="action" value="insert">
                    <div class="row mb-5">
                        <div class="item col-md-9">
                            <p>Item:</p>
                            <input type="text" list="pastItems" name="name" autofocus>
                            <datalist id="pastItems">
<?php foreach($suggestionData as $item){ ?>
                                <option value="<?=htmlspecialchars($item["name"])?>">
<?php } ?>
                            </datalist>
                        </div>
                        <div class="amount col-md-3">
                            <p>Amount:</p>
                            <input type="number" value="1" min="1" name="amount">
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="submit col-md-1">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/delete.js"></script>
    <script src="js/edit.js"></script>
    <script src="js/swap.js"></script>
</body>
</html>