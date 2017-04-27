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

function donate(pid) {
    var donation = document.getElementById("donation").value;
    donation = parseInt(donation);

    $.ajax({
        method: 'POST',
        url: '/Flint/model/donate.php',
        data: {
            donation: donation,
            pid: pid
        },
        success: function(result) {
            if (parseInt(result) > 0) {
                //thank the donor
                document.getElementById('reply').innerHTML = "Thank you for donating!";
                //update current funds
                document.getElementById('donation-scale').value = result;
                document.getElementById('current-funds').innerHTML = "Current funds: "+result;
            } else {
                document.getElementById('reply').innerHTML = result;
            }
        }
    });
}

function like(pid) {
    $.ajax({
        method: 'POST',
        url: '/Flint/model/project_like.php',
        data: {
            pid: pid
        },
        success: function(result) {
            if (result == 'liked') {
                var likes = document.getElementById('likes');
                var count = parseInt(likes.innerHTML.charAt(0));
                count++;
                likes.innerHTML = count.toString() + " likes";
                //remove the button
                var btn = document.getElementById('like-button');
                btn.parentNode.removeChild(btn);
            }
        }
    });
}