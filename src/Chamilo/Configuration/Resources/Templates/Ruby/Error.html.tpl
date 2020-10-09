<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>{ error_code } - { error_title }</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

    <script>
        function goBack() {
            window.history.go(-1);
            return false;
        }
    </script>

    <link rel="stylesheet" href="Chamilo/Libraries/Resources/Css/Ruby/ErrorPages.min.css">
</head>
<body>

<div class="main">
    <div class="error-message-container lead text-justify text-muted">
        <div class="media">
            <div class="media-body">
                <h1>{ error_code }</h1>
            </div>
            <div class="media-right media-middle">
                <a href="#">
                    <img class="media-object"" src="Chamilo/Libraries/Resources/Images/Aqua/LogoHeader.png"/>
                </a>
            </div>
        </div>

        <div class="clearfix">{ error_content }</div>
        <br/>

        <div class="btn-group btn-group-justified" role="group">
            <a href="#" class="btn btn-success" role="button" onclick="goBack();">{ return_button_content }</a>
        </div>
    </div>
</div>

</body>
</html>