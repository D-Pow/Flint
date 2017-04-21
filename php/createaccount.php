<!DOCTYPE html>
<html>
<head>
    <!--Force browser to pull page from server-->
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <link rel="stylesheet" href="/Flint/css/common.css" />
    <title>Create new account</title>
</head>
<body>
    <div id='container'>
    <h2 style="font-family: cursive;">Create new account</h2>
        <table>
            <tr>
                <td><b>Username:</b></td>
                <td><input type="text" id="user" name="username"></td>
            </tr>
            <tr>
                <td><b>Password:</b></td>
                <td><input type="password" id="pass" name="password"></td>
            </tr>
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" id="name" name="name"></td>
            </tr>
            <tr>
                <td><b>Credit card number:</b></td>
                <td><input type="text" id="ccn" name="ccn"></td>
            </tr>
            <tr>
                <td colspan="2">
                <input type="button" value="Create Account" onclick="login(1)">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p id='feedback'></p>
                </td>
            </tr>
        </table>
    </div>
    <br />
    <br />
    <script>
        function displayFeedback(message) {
            document.getElementById("feedback").innerHTML = message;
        }

        function login(createNew) {
            var username = document.getElementById("user").value;
            var password = document.getElementById("pass").value;
            var name = document.getElementById("name").value;
            var ccn = document.getElementById("ccn").value;

            $.ajax({
                method: 'POST',
                url: '/Flint/php/login.php',
                data: {
                    createNew: createNew,
                    username:  username,
                    password:  password,
                    name: name,
                    ccn: ccn
                },
                success: function(result) {
                    //Username or password were blank
                    if (result == 'no values') {
                        displayFeedback("Please enter all fields");
                    //If right username, wrong password
                    } else if (result == "wrong password") {
                        displayFeedback("Incorrect username-password combination");
                    //If no accounts exist with that username
                    } else if (result == "no usernames") {
                        displayFeedback("That username doesn't exist");
                    //Username already taken
                    } else if (result == "user exists") {
                        displayFeedback("That username is already taken");
                    //Correct login; redirect
                    } else {
                        //Make sure cookie exists first
                        if (document.cookie) {
                            window.location.href = "/Flint/php/home.php";
                        }
                    }
                }
            });
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</body>
</html>