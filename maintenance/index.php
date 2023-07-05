<!DOCTYPE html>
<html>
<head>
    <title>LearnLoop</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html {
            position: relative;
            min-height: 100%;
        }
        body {
            margin: 0 0 100px;
        }
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
        }
        [v-cloak] {
             display: none;
        }
        a {
            color:black;
        }
        #spacer {
            height: 30px;
        }
        .quote {
            width: auto;
            text-align: center;
        }
        .fa-question-circle {
            vertical-align: middle;
        }
        .copyFrom {
            background-color: white;
            width: 70%;
            padding: 10px;
        }
        #reload {
            transition: all .5s ease-in-out;
        }
        #reload:hover {
            color:teal;
            transform: scale(1.4) rotate(120deg);
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm bg-info navbar-light">
            <a class="navbar-brand" href="/"><img src="logo.png" alt="LearnLoop Logo" height="50"></a>
        </nav>
        <div class="container">
            <br><br>
            <section id="welcome">
                <div class="card p-2 bg-light" id="updateInfo">
                    LearnLoop is under maintenance. This will be finished at some point this evening.
                </div>
            </section>
        </div>
        <?php include "../footer.php";?>
    </div>
    <script>
        window.history.replaceState('maintenance', 'LearnLoop', '/'); //means that when user refreshes the page, will try to load homepage again
    </script>
</body>
</html>


