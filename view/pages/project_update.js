function saveChanges(pid) {
    var description = document.getElementById("description").value;
    var title = document.getElementById('title').value;

    $.ajax({
        method: 'POST',
        url: '/Flint/model/project_update.php',
        data: {
            description: description,
            pname: title,
            pid: pid
        },
        success: function(result) {
            alert(result);
        }
    });
}