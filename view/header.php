<!--Header content-->
<div id='header'>
<div id='nav'>
<h2 id='user-title'>Welcome <?php echo $_SESSION['username']; ?>!</h2>
<ul>
    <li>
        <a id='home'
            href='/Flint/?controller=pages&action=home'>Home</a>
    </li>
    <li>
        <a id='posted-projects'
            href='/Flint/?controller=pages&action=posted_projects'>My Projects</a>
    </li>
    <li>
        <a id='new-project'
            href='/Flint/?controller=pages&action=new'>New Project</a>
    </li>
    <li>
        <a id='edit-profile'
            href='/Flint/?controller=pages&action=user&user=<?php 
            echo $_SESSION['username']; ?>'>Profile</a>
    </li>
    <li>
        <a id='logout'
            href='/Flint/?controller=pages&action=logout'>Logout</a>
    </li>
</ul>
</div>
<div id='search-content'>
    <input id='search-bar' type='text' placeholder='Search'
            onkeydown="search(event)" onkeyup="filterPrevSearches()">
        <?php
        require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/view_log.php');
        $searches = ViewLog::getRecentSearches($_SESSION['username']);
        if ($searches) {
            //only show previous search content if previous searches exist
            echo "<div id='prev-search-arrow'></div>";
            echo "<ul id='previous-searches'>";
            foreach ($searches as $search) {
                echo "<li class='search-item'>";
                echo "<a href='/Flint/?controller=pages&action=search&q="
                    .$search."'>".$search."</a>";
                echo "</li>";
            }
            echo "</ul>";
        }
        ?>

    <script>
        /**
         * Searches the keyword once the user presses `Enter`
         */
        function search(e) {
            var code;
            if (e != 13) {
                code = e.which || e.keyCode;
            }
            if (e == 13 || code == 13) {
                window.location.href=
                    "/Flint/?controller=pages&action=search&q=" + 
                    encodeURIComponent(  //encodes input for safe url handling
                        document.getElementById("search-bar").value
                    );
                e.stopPropagation();
            }
            return;
        }

        /**
         * Reduces the previous searches by what is typed in the bar
         */
        function filterPrevSearches() {
            var input = document.getElementById('search-bar').value;
            input = input.toLowerCase();
            var li = document.getElementsByClassName('search-item');
            var itemExists = false;
            for (var i = 0; i < li.length; i++) {
                var a = li[i].getElementsByTagName('a')[0];
                var item = a.innerHTML.toLowerCase();
                //indexOf searches for full `filter` string in the `input`
                //-1 if string not present
                if (item.indexOf(input) >= 0) {
                    li[i].style.display = '';
                    itemExists = true;
                } else {
                    //display makes the element not take up space
                    li[i].style.display = 'none';
                }
            }
            var arrow = document.getElementById('prev-search-arrow');
            if (!itemExists) {
                arrow.style.display = 'none';
            } else {
                arrow.style.display = '';
            }
        }
    </script>
</div>
</div>

<?php
    function updateSearchList() {
        ?>
        <script>
            //delete all old search items
            var ul = document.getElementById('previous-searches');
            while (ul.hasChildNodes()) {
                ul.removeChild(ul.lastChild);
            }
        </script>
        <?php
        //output new search items
        $searches = ViewLog::getRecentSearches($_SESSION['username']);
        foreach ($searches as $search) {
            ?>
            <script>
            var li = document.createElement('li');
            li.className = 'search-item';
            var item = '<?php echo $search; ?>';
            var a = document.createElement('a');
            a.href = '/Flint/?controller=pages&action=search&q=' + item;
            a.innerHTML = item;
            li.appendChild(a);
            ul.appendChild(li);
            </script>
            <?php
        }
    }
?>