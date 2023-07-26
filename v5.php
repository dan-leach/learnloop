<!DOCTYPE html>
<html>
<head>
    <title>LearnLoop</title>

    <?php include "assets/dependencies.php";?>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm bg-info navbar-light">
            <a class="navbar-brand nav-item" href="/"><img src="/assets/img/logo.png" alt="LearnLoop Logo" height="50"></a>
            <h2>DEVELOPMENT VERSION</h2>
        </nav>
        <div class="container">
            <br><br>
            <section v-if="show.welcome" id="welcome" v-cloak>
                <p>Welcome to LearnLoop. Please select from the options below.</p><br>
                <div class="card-deck">
                    <div class="card p-2 bg-info" id="feedbackCard">
                        <h4 class="card-title">Feedback</h4>
                        <p class="card-text">Quickly and easily gather anonymous feedback on teaching.
                            <br><br>
                            <div class="text-center">
                                <button class="btn btn-primary" v-on:click="launchFeedback()">Go</button>
                            </div>
                        </p>
                    </div>
                    <div class="card p-2 bg-info" id="interactCard">
                        <h4 class="card-title">Interact</h4>
                        <p class="card-text">Engage your audience with live interactions.
                            <br><br>
                            <div class="text-center">
                                <button class="btn btn-primary" v-on:click="launchInteract()">Go</button>
                            </div>
                        </p>
                    </div>
                </div>
                <br>
                <div class="card p-2 bg-light" id="updateInfo">
                    Update July 2023:
                    <ul>
                        <li>Latest update info...</li>
                </div>
                <br><br>
            </section>
            <section v-if="show.loader" id="loader">
                <br><br>
                <div class="spinner-border"></div>    
                Please wait... LearnLoop is loading
            </section>
            <div id="logger"></div>
            <div id="spacer"></div>
        </div>
        <?php include 'assets/footer.php';?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!--add to dependencies-->
    <script src="assets/loader-v1.js"></script>
</body>
</html>


