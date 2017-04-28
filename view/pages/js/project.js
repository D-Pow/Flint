/**
 * Allows the owner to update the project's title and description
 */
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

/**
 * Allows a user to donate to a project
 */
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

/**
 * Allows a non-owner to like a project
 */
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

/**
 * Allows an owner to post an update or a user to post a comment
 */
function post(pid, owner, username) {
    var content = document.getElementById('post-content').value;

    $.ajax({
        method: 'POST',
        url: '/Flint/model/project_post.php',
        data: {
            pid: pid,
            owner: owner,
            content: content
        },
        success: function(result) {
            if (result == "Post successful") {
                //make new post and insert it above all other posts
                var previousPost;
                if (owner) {
                    previousPost = document.getElementsByClassName("update-post")[0];
                } else {
                    previousPost = document.getElementsByClassName("comment-post")[0];
                }
                //make new post
                var newPost = `
                    <div class='entry comment-post'> 
                        <h4> 
                            <a class='entry-title'
                                href='/Flint/?controller=pages&action=user&user=`
                                + username + "'>" + username + `</a></h4>
                        <p>` + content + `</p>
                    </div> 
                `;
                //div must be appended first because insertBefore only accepts one node
                //at a time
                var wrapper = document.createElement('div');
                document.body.insertBefore(wrapper, previousPost);
                wrapper.innerHTML = newPost;
            } else {
                alert(result);
            }
        }
    });
}