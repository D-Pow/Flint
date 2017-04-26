function saveChanges() {
    var title = document.getElementById('title').value;
    var description = document.getElementById('description').value;
    var minfunds = document.getElementById('minfunds').value;
    var maxfunds = document.getElementById('maxfunds').value;
    var date = document.getElementById('date').value;

    $.ajax({
        method: "POST",
        url: '/Flint/model/new_project.php',
        data: {
            title: title,
            description: description,
            minfunds: minfunds,
            maxfunds: maxfunds,
            date: date
        },
        success: function(result) {
            alert(result);
        }
    });
}