function donate(username, amount) {
    var donation = document.getElementById("donation").value;
    donation = parseInt(donation);

    $.ajax({
        method: 'POST',
        url: '/Flint/model/donate.php',
        data: {
            username: username,
            donation: amount
        },
        success: function(result) {
            //Username or password were blank
            if (result == 'no values') {
                displayFeedback("Please enter both username and password");
            //If right username, wrong password
            } else if (result == "wrong password") {
                displayFeedback("Incorrect username-password combination");
            //If no accounts exist with that username
            } else if (result == "no usernames") {
                displayFeedback("That username doesn't exist");
            //Correct login; redirect
            } else if (result == "accept login") {
                //Ensure php session began correctly
                if (document.cookie) {
                    window.location.href = "/Flint/?controller=pages&action=home";
                }
            } else {
                displayFeedback(result);
            }
        }
    });
}