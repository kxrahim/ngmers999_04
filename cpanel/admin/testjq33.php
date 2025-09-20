<!DOCTYPE html>
<html>
<head>
    <title>Ajax Search Example</title>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Search for users</h2>
            <input type="text" name="search" id="search" autocomplete="off" placeholder="search user name here....">
            <div id="result"></div>
<!--            <div id="studentId"></div>-->
<!--            <div id="fullname"></div>-->

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#search").keyup(function(){
            var query = $(this).val();
            if (query != "") {
                $.ajax({
                    type : 'POST',
                    url : 'fetchjq3.php',
                    dataType : 'json',
                    data: {query:query},
                    success : function(data){
                        $('#result').removeClass().addClass((data.error === true) ? 'error' : 'success')
                            .html(data.msg).show();
                        if (data.error === true)
                            $('#demoForm').show();
                    },
                    error : function(XMLHttpRequest, textStatus, errorThrown) {
                        $('#result').removeClass().addClass('error')
                            .text('There was an error.').show(500);
                        $('#demoForm').show();
                    }
                });
            } else {
                $('#output').css('display', 'none');
            }
        });
    });
</script>
</body>
</html>