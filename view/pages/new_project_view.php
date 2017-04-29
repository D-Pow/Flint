<link rel="stylesheet" href="/Flint/view/pages/css/project.css" />

<div id="container">
    <script src='/Flint/view/pages/js/new_project.js'></script>
    <h1>Title: <input id='title' type='text'></h1>
    <br />

    <h3>Description:</h3>
    <textarea id='description' type='text' rows='10' cols='50'></textarea>
    <br />
    
    <h3>Funding:</h3>
    <p>Minimum funds: <input id='minfunds' type='number' step='1' 
            title="Whole numbers" pattern="[0-9]"></p>
    <p>Maximum funds: <input id='maxfunds' type='number' step='1' 
            title="Whole numbers" pattern="[0-9]"></p>
    <h3>Campaign end time: <input id='date' type='date'></h3>
    <h3 style='margin: 0;'>Tags: </h3>
    <p style='margin: 0;'>(Please separate tags using commas)</p>
    <input type='text' id='tags-input'>
    <br />
    <br />
    <button id='save' onclick='saveChanges()'>Post Project</button>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>