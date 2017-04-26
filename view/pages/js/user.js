function saveChanges() {
    var email = document.getElementById('email').value;
    var addr = document.getElementById('str-addr').value;
    var city = document.getElementById('city').value;
    var state = document.getElementById('state').value;
    var interests = document.getElementById('interests').value;

    $.ajax({
        method: 'POST',
        url: '/Flint/model/user_update.php',
        data: {
            email: email,
            addr: addr,
            city: city,
            state: state,
            interests: interests
        },
        success: function(result) {
            alert(result);
        }
    });
}