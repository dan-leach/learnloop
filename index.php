<?php
    //redirect v1 links
    if(isset($_GET['preload'])) {
        $preload = $_GET['preload'];
        $params = substr($_SERVER[REQUEST_URI], strpos($_SERVER[REQUEST_URI], "?") + 1);    
        $redirect = 'Location: ' . 'https://feedback.danleach.uk/v1/?' . $params;
        header($redirect);
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback Tool</title>

    <!--Bootstrap 4-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- icons-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    
    <!--favicons-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#17a2b8">
    <meta name="msapplication-TileColor" content="#17a2b8">
    <meta name="theme-color" content="#17a2b8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    
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
    </style>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm bg-info navbar-light">
            <a class="navbar-brand" href="/"><img src="logo.png" alt="Feedback Tool Logo" height="50"></a>
        </nav>
        <div id="alert"></div>
        <div class="container">
            <section v-if="show.loader" id="loader">
                <br><br>
                <div class="spinner-border"></div>    
                Please wait... Feedback Tool is loading
            </section>
            <section v-if="show.welcome" id="welcome">
                <br><br>
                <h2>Welcome to the feedback tool.</h2>
                You can use this tool to quickly and easily request or provide anonymous feedback on a teaching session.
                <br><br>
                <div class="card-deck">
                    <div class="card p-2 bg-info" id="giveFeedbackCard">
                        <h4 class="card-title">Give feedback on a session you attended</h4>
                        <p class="card-text">The session facilitator will have provided you with a session ID code. Enter the code in the search box below, then click 'Give feedback'.
                            <br>
                            <div class="input-group">
                                <input v-model="fetchID" type="text" class="form-control" placeholder="Session ID" autocomplete="off">
                                <button type="button" id="getExistingSession" class="btn btn-primary" v-on:click="getSession">Give feedback</button>
                            </div>
                        </p>
                    </div>
                    <div class="card p-2 bg-info" id="createSessionCard">
                        <h4 class="card-title">Request feedback on a session you delivered</h4>
                        <p class="card-text">To set up a new feedback request, and generate a code or link to share with attendees, click the 'create new session' button below.
                            <br>
                            <div class="input-group">
                                <button class="btn btn-primary" id="submitGiveFeedback" v-on:click="hideComponent('all'); showComponent('createSession')">Create new session</button>
                            </div>
                        </p>
                    </div>
                </div>
                <br>
                <div class="quote" v-html="quote"></div>
            </section>
            <section v-if="show.giveFeedback" id="giveFeedback">
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
                                <span class="input-group-text">Overall score: ({{feedback.overallScore}}/100)</span>
                            </div>
                            <input type="range" v-model="feedback.overallScore" style="width:80%; margin:10px;" id="overallScoreRange" placeholder="" name="overallScoreRange" autocomplete="off">
                            <input type="text" v-model="feedback.overallScore" class="form-control-range" id="overallScore" placeholder="" name="overallScore" autocomplete="off" required hidden>
                            <div class="invalid-feedback">Please indicate an overall score using the slider.</div>
                        </div>
                    </div>
                </form>
                <button class="btn btn-primary" id="submitGiveFeedback" v-on:click="giveFeedback">Give Feedback</button>
            </section>
            <section v-if="show.completedFeedback" id="completedFeedback">
                <br><br>
                <div class="card-deck">
                    <div class="card p-2 bg-success" id="giveFeedbackCard">
                        <h4 class="card-title">Thank you for providing feedback</h4>
                        <p class="card-text">Your anonymous feedback will be returned to the session facilitator.</p>
                        <div v-if="sDetails.sCert">
                            The facilitator has provided a certificate for this session for you to download.<br>
                            <form id="getCertificateForm" class="needs-validation" method="post" action="api/getCertificate.php" novalidate>
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
            <section v-if="show.createSession" id="createSession">
                <br><strong>Please provide details of the session you are requesting feedback for.</strong><br><br>
                <form id="createSessionForm" class="needs-validation" novalidate>
                    <div class="form-group">
                      <label for="sName">Session title:</label>
                      <input type="text" v-model="sDetails.sName" class="form-control" id="sName" placeholder="Please enter the title of the teaching session..." name="sName" autocomplete="off" required>
                      <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="sDate">Session date:</label>
                        <input type="date" v-model="sDetails.sDate" class="form-control" id="sDate" placeholder="" name="sDate" autocomplete="off" required>
                        <div class="invalid-feedback">Please select a date.</div>
                    </div>
                    <div class="form-group">
                        <label for="fName">Facilitator name:</label>
                        <input type="text" v-model="sDetails.fName" class="form-control" id="fName" placeholder="Please enter the name of the session facilitator..." name="fName" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="fEmail">Facilitator email:</label>
                        <input type="email" v-model="sDetails.fEmail" class="form-control" id="fEmail" placeholder="Please enter the email for feedback to be returned to..." name="fEmail" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </form>
                <br>
                <button class="btn btn-info" id="showCertificateOptions" v-on:click="showCertificateOptions">Create session certificate</button>
                <a href="#" data-toggle="modal" data-target="#certificateOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="certificateOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Create session certificate</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                You can create a certificate for attendees of you session to download after they've provided completed the feedback form. This is a good way of encouraging attendees to provide feedback.
                                <br><br>
                                From the create new session page click the 'create session certificate'. You can toggle the option on or off if you change your mind.
                                <br><br>
                                This is optional and you don't need to create a certificate to collect feedback.
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="show.certificateOptions">
                    <div v-if="sDetails.sCert">
                        Attendees will be able to download a certificate for this session after providing feedback. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                    </div>
                    <div v-if="!sDetails.sCert">
                        Attendees will not be able to download a certificate for this session after providing feedback. <i style="color:red"class="fas fa-times"></i>
                    </div>
                </div>
                <br><br>
                <button class="btn btn-primary" id="submitCreateSession" v-on:click="createSession">Create session</button>
            </section>
            <section v-if="show.requestFeedback" id="requestFeedback">
                <br><br>
                <div class="card-deck">
                    <div class="card p-2 bg-success" id="giveFeedbackCard">
                        <h4 class="card-title">Your session was created successfully</h4>
                        <p>You can copy the feedback invitation below, or just the link, to share with the attendees of your session.</p>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Feedback invitation:</span>
                                </div>
                                <div id="inviteFull" v-html="invite.full" class="copyFrom"></div>
                                <div class="input-group-append">
                                    <button type="button" id="copyFull" class="btn btn-primary" v-on:click="copy('inviteFull')" v-if="showCopyBtns">Copy</button>
                                </div>
                            </div>
                            <br>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Feedback link only:&nbsp;&nbsp;</span>
                                </div>
                                <div id="inviteLink" v-html="invite.link" class="copyFrom"></div>
                                <div class="input-group-append">
                                    <button type="button" id="copyLink" class="btn btn-primary" v-on:click="copy('inviteLink')" v-if="showCopyBtns">Copy</button>
                                </div>
                            </div>
                            <br>
                            You can insert this QR code at the end of a powerpoint presentation to allow your attendees to link directly with their smartphones to give feedback. Right click on the QR code image and select "Save image as...", then insert the saved image into your presentation.<br>
                            <strong>The QR code may take a few moments to be generated...</strong>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">QR Code:&nbsp;&nbsp;</span>
                                </div>
                                <div id="inviteQR" v-html="invite.qr" class="copyFrom" style="width:80%"></div>
                            </div>
                            <br>
                            <strong>Please ensure you check your junk mail for feedback responses which will be sent from feedback@danleach.uk</strong>
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
                    <a class="nav-link" href="/">Feedback Tool - Version 2.0</a>
                  </li>
                <li class="nav-item">
                  <a class="nav-link" href="https://danleach.uk">Created by Dan Leach</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mailto:web@danleach.uk">Contact: web@danleach.uk</a>
                </li>
              </ul>
        </footer>
    </div>
    <!--Vue development mode - remember to change to production version-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="app.js"></script>
</body>
</html>


