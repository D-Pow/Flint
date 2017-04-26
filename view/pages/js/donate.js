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