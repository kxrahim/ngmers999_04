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
<!--            <div id="output"></div>-->
            <div id="studentId"></div>
            <input type="text" name="fullname" id="fullname">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#search").keyup(function(){
            var query = $(this).val();
            if (query != "") {
                $.ajax({
                    url: 'fetchjq4.php',
                    method: 'POST',
                    data: {query:query},
                    success: function(data){
                        data = JSON.parse(data);

                        $('#studentId').html(data.studentId);
                        //$('#fullname').html(data.fullname).val();
                        $('#fullname').val(data.fullname);

                        $('#output').css('display', 'block');

                        $("#search").focusout(function(){
                            $('#output').css('display', 'none');
                        });
                        $("#search").focusin(function(){
                            $('#output').css('display', 'block');
                        });
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