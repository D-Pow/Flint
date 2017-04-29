function saveChanges() {
    var title = document.getElementById('title').value;
    var description = document.getElementById('description').value;
    var minfunds = document.getElementById('minfunds').value;
    var maxfunds = document.getElementById('maxfunds').value;
    var date = document.getElementById('date').value;
    var tagsInput = document.getElementById('tags-input').value;
    var tags = tagsInput.split(",");
    for (var i = 0; i < tags.length; i++) {
        //strip whitespace
        tags[i] = tags[i].trim();
    }

    $.ajax({
        method: "POST",
        url: '/Flint/model/new_project.php',
        data: {
            title: title,
            description: description,
            minfunds: minfunds,
            maxfunds: maxfunds,
            date: date,
            tags: tags
        },
        success: function(result) {
            alert(result);
            if (result == 'Project posted!') {
                window.location.href = '/Flint/?controller=pages&action=home'
            }
        }
    });
}