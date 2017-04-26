function rate(pid) {
    var rating = $("input[name=rating]:checked").val();

    $.ajax({
        method: 'POST',
        url: '/Flint/model/rate.php', 
        data: {
            pid: pid,
            rating: rating
        },
        success: function(results) {
            alert(results);
            window.location.href="/Flint/?controller=pages&action=home"
        }
    });
}