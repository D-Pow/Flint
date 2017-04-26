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