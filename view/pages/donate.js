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
            document.getElementById('reply').innerHTML = result;
        }
    });
}