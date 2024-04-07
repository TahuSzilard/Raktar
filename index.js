$(document).ready(function() {
    $('#newDataBtn').click(function() {
        $('#form').toggle();
    });

    $('#addDataBtn').click(function() {
        var storeName = $('#store_name').val();
        var storeAddress = $('#store_address').val();
        var shelfName = $('#shelf_name').val();
        var rowName = $('#row_name').val();
        var columnName = $('#column_name').val();
        var productName = $('#product_name').val();

        if (storeName === "" || storeAddress === "" || shelfName === "" || rowName === "" || columnName === "" || productName === "") {
            alert("Minden mezőt ki kell tölteni!");
        } else {
            $('#newDataForm').submit();
        }
    });
    $(document).on('click', '.delBtn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            type: 'POST',
            url: 'delete.php',
            data: {delete_id: id},
            success: function(response) {
                alert(response); 
                location.reload(); 
            },
            error: function(xhr, status, error) {
                alert("Hiba történt a törlés közben: " + error);
            }
        });
    });
});
