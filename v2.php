<!DOCTYPE html>
<html>
<head>
    <title>LearnLoop</title>

    <?php include "dependencies.php";?>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm bg-info navbar-light">
            <a class="navbar-brand" href="/"><img src="logo.png" alt="LearnLoop Logo" height="50"></a>
        </nav>
        <div id="alert"></div>
        <div class="container">
            <br><br>
            <section v-if="show.loader" id="loader">
                <br><br>
                <div class="spinner-border"></div>    
                Please wait... LearnLoop is loading
            </section>
            <section v-if="show.welcome" id="welcome" v-cloak>
                <h1>LearnLoop has been upgraded.</h1>
                <a href="https://learnloop.co.uk">Click here to go to the latest version</a>.
                <br><br><br>
                <div class="quote" v-html="quote"></div>
            </section>
            <section v-if="show.giveFeedback" id="giveFeedback" v-cloak>
                <br><strong>Please provide some feedback to {{sDetails.fName}} regarding their session '{{sDetails.sName}}' delivered on {{sDetails.sDate}}.</strong><br><br>
                <form id="giveFeedbackForm" class="needs-validation" novalidate>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Positive Comments:</span>
                            </div>
                            <textarea rows="8" v-model="feedback.positiveComments" class="form-control" id="positiveComments" placeholder="Please provide some feedback about what you enjoyed about this session..." name="positiveComments" autocomplete="off" required></textarea>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Constructive Comments:</span>
                            </div>
                            <textarea rows="8" v-model="feedback.constructiveComments" class="form-control" id="constructiveComments" placeholder="Please provide some feedback about ways this session could be improved..." name="constructiveComments" autocomplete="off" required></textarea>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Overall score ({{feedback.overallScore}}/100):</span>
                            </div>
                            <input type="range" v-model="feedback.overallScore" style="width:80%; margin:10px;" id="overallScoreRange" placeholder="" name="overallScoreRange" autocomplete="off" oninput="app.scoreChange()" onchange="app.scoreChange()">
                            <input type="text" v-model="feedback.overallScore" class="form-control-range" id="overallScore" placeholder="" name="overallScore" autocomplete="off" required hidden>
                            <div class="invalid-feedback">Please indicate an overall score using the slider.</div>
                        </div>
                        <div class="input-group">
                            <textarea rows=2 v-model="feedback.scoreText" class="form-control" id="scoreText" placeholder="" name="scoreText" autocomplete="off" readonly></textarea>
                        </div>
                    </div>
                </form>
                <button class="btn btn-primary" id="submitGiveFeedback" v-on:click="giveFeedback">Give Feedback</button>
            </section>
            <section v-if="show.completedFeedback" id="completedFeedback" v-cloak>
                <br><br>
                <div class="card-deck">
                    <div class="card p-2 bg-success" id="giveFeedbackCard">
                        <h4 class="card-title">Thank you for providing feedback</h4>
                        <p class="card-text">Your anonymous feedback will be returned to the session facilitator.</p>
                        <div v-if="sDetails.sCert">
                            The facilitator has provided a certificate for this session for you to download.<br>
                            <form id="getCertificateForm" class="needs-validation" method="post" action="api-v2/getCertificate.php" novalidate>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Name for certificate:</span>
                                        </div>
                                        <input type="text" v-model="sDetails.sIdent" class="form-control" id="sIdent" placeholder="Enter your name as you would like it to appear on your certificate..." name="sIdent" autocomplete="off" hidden required>
                                        <input type="text" v-model="certName" class="form-control" id="certName" placeholder="Enter your name as you would like it to appear on your certificate..." name="certName" autocomplete="off" required>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                </div>
                            </form>
                            <button class="btn btn-info" id="downloadCertificate" v-on:click="downloadCertificate">Download certificate</button>
                        </div>
                    </div>
                </div>
            </section>
            <div id="logger"></div>
            <div id="spacer"></div>
        </div>
        <footer class="bg-info">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="/">LearnLoop - Version 2.0</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="https://danleach.uk">Created by Dan Leach</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mailto:mail@learnloop.co.uk">Contact: mail@learnloop.co.uk</a>
                </li>
                <li>
                    <a class="nav-link" href="/privacy-policy/">Privacy policy</a>
                </li>
                <br>
            </ul>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="app-v2.js"></script>
</body>
</html>


