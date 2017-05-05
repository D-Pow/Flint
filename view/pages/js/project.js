/**
 * Allows the owner to update the project's title and description
 */
function saveChanges(pid) {
    var description = document.getElementById("description").value;
    var title = document.getElementById('title').value;
    var tagsInput = document.getElementById('tags-input').value;
    var tags = tagsInput.split(",");
    for (var i = 0; i < tags.length; i++) {
        //strip whitespace
        tags[i] = tags[i].trim();
    }

    $.ajax({
        method: 'POST',
        url: '/Flint/model/project_update.php',
        data: {
            description: description,
            pname: title,
            pid: pid,
            tags: tags
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
 * Allows an owner to mark that a project has been completed
 */
function finishProject(pid) {
    //ensure user really wants to complete the project
    if (!confirm("Are you sure you want to mark the project as completed? \n" +
        "This action cannot be undone.")) {
        return
    }
    //mark project as completed
    var completed = true;
    $.ajax({
        method: 'POST',
        url: '/Flint/model/project_finish.php',
        data: {pid: pid},
        success: function(result) {
            if (result == 'success') {
                window.location.reload(true); //force reload from server
            } else {
                alert(result);
            }
        }
    });
}

/**
 * Allows an owner to post an update or a user to post a comment
 */
function post(pid, owner, username) {
    var form = document.getElementById('post-form');
    var d = new FormData(form); //includes post content and file uploads (if owner)
    //add pid and owner to form data
    d.append('pid', pid);
    d.append('owner', owner);
    //get content for putting post on screen
    var content = document.getElementById('post-content').value;

    $.ajax({
        method: 'POST',
        url: '/Flint/model/project_post.php',
        processData: false, // Don't process the file arrays into strings
        contentType: false, // Set content type to false so doesn't send as normal form
        data: d,
        success: function(result) {
            if (result == "Post successful") {
                //make new post and insert it above all other posts
                if (owner) {
                    var className = "update-post";
                } else {
                    var className = "comment-post";
                }
                var previousPost = document.getElementsByClassName(className)[0];
                //make new post
                var newPost = `
                    <div class='entry ` + className + `'> 
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
                //if previous post exists, insert before it
                if (previousPost) {
                    document.body.insertBefore(wrapper, previousPost);
                } else {
                    document.body.append(wrapper);
                }
                wrapper.innerHTML = newPost;
                //clear input after posting
                var input = document.getElementById('post-content');
                var btn = document.getElementById('post-button');
                input.parentNode.removeChild(input);
                btn.parentNode.removeChild(btn);
                //if owner, remove file uploading button
                if (owner) {
                    var fileInput = document.getElementById('uploader');
                    fileInput.parentNode.removeChild(fileInput);
                }
                document.getElementById('post-thanks').innerHTML = "Thanks for posting";
            } else {
                alert(result);
            }
        }
    });
}