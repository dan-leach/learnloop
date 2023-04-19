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
        <div class="container">
            <br><br>
            <section v-if="show.loader" id="loader">
                <br><br>
                <div class="spinner-border"></div>    
                Please wait... LearnLoop is loading
            </section>
            <section v-if="show.welcome" id="welcome" v-cloak>
                Quickly and easily gather anonymous feedback on teaching.
                <br><br>
                <!--<div class="card p-2 bg-primary text-white" id="updateInfo">
                    <strong>Update November 2022:</strong> LearnLoop has been updated to allow gathering of feedback on multiple sessions on a single link, for example for teaching days. Feedback will no longer be sent directly to your email - instead you will be able to view the collected feedback here using your session ID and PIN. I'll create a how to guide soon, but in the meantime feel free to contact me for help (email at the bottom of this page).
                </div>
                <br><br>-->
                <div class="card-deck">
                    <div class="card p-2 bg-info" id="giveFeedbackCard">
                        <h4 class="card-title">Give feedback</h4>
                        <p class="card-text">The session facilitator will have provided you with a session ID code. Enter the code in the search box below, then click 'Give feedback'.
                            <br>
                            <div class="input-group">
                                <input v-model="session.id" type="text" class="form-control" placeholder="Session ID" autocomplete="off">
                                <button type="button" id="getExistingSession" class="btn btn-primary" v-on:click="getSession">Give feedback</button>
                            </div>
                        </p>
                    </div>
                    <div class="card p-2 bg-info" id="createSessionCard">
                        <h4 class="card-title">Request feedback</h4>
                        <p class="card-text">To set up a new feedback request, and generate a link or QR code to share with attendees, click 'Create new session'.
                            <br>
                            <div class="text-center">
                                <button class="btn btn-primary" id="submitGiveFeedback" v-on:click="hideComponent('welcome'); showComponent('createSession')">Create new session</button>
                            </div>
                        </p>
                    </div>
                    <div class="card p-2 bg-info" id="viewFeedbackCard">
                        <h4 class="card-title">View feedback</h4>
                        <p class="card-text">Enter the session ID code from when you set up the request in the search box below, then click 'View feedback'.
                            <br>
                            <div class="input-group">
                                <input v-model="viewID" type="text" class="form-control" placeholder="Session ID" autocomplete="off">
                                <button type="button" id="goToEnterPin" class="btn btn-primary" v-on:click="enterPIN">View feedback</button>
                            </div>
                        </p>
                    </div>
                </div>
                <br>
                <div class="quote" v-html="quote"></div>
            </section>
            <section v-if="show.enterPin" id="enterPin" v-cloak>
                <div class="card p-2 bg-info" id="enterPinCard">
                    <h4 class="card-title">View feedback on your session</h4>
                    <p class="card-text">You will need the pin from your confirmation email.
                        <br>
                        <div class="input-group">
                            <input v-model="session.pin" type="password" class="form-control" placeholder="Session PIN" autocomplete="off" v-on:keyup.enter="viewFeedback()">
                            <button type="button" id="submitPin" class="btn btn-primary" v-on:click="viewFeedback">View feedback</button>
                        </div>
                        <div>Lost your PIN? <a href='#' v-on:click="resetPIN">Click to reset.</a></div>
                    </p>
                </div>
                <br>
            </section>
            <section v-if="show.viewFeedback" id="viewFeedback" v-cloak>
                Viewing feedback for <strong>'{{session.title}}'</strong> by <strong>{{session.name}}</strong> on <strong>{{session.date}}</strong>.<br>
                <br>
                <form method="post" action="api-v3/viewFeedbackPDF.php">
                    <input v-model="session.id" type="text" name="id" readonly hidden>
                    <input v-model="session.pin" type="text" name="pin" readonly hidden>

                    <div class="text-center">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Download feedback as PDF</span>
                            </div>
                            <select id="selectSubID" name="subID" class="form-control" v-if="session.subsessions">
                                <option value="">Overall feedback and all sessions</option>
                                <option v-for="subsession in session.subsessions" :value="subsession.id">Just '{{ subsession.title }}'</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="btnViewFeedbackPDF">Go</button>
                            </div>
                        </div>
                    </div>
                </form>
                <br>
                <h4>Positive comments:</h4>
                <span v-for="item in session.feedback.positive">{{item}}<br></span>
                <br>
                <h4>Constructive comments:</h4>
                <span v-for="item in session.feedback.negative">{{item}}<br></span>
                <div v-for="(question, index) in session.questions">
                    <br>
                    <h4>{{question.title}}</h4>
                    <div v-if="question.type == 'text'">
                        <span v-for="response in question.responses">{{response}}<br></span>
                    </div>
                    <div v-if="question.type == 'checkbox' || question.type == 'select'">
                        <span v-for="option in question.options">{{option.title}}: {{option.count}}<br></span>
                    </div>
                </div>
                <br>
                <h4>Overall Score: {{session.feedback.score}}/100</h4>
                <br>
                <div v-if="session.subsessions">
                    <h4>Sessions:</h4>
                    <table id="subsessionFeedback" class="table">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Positive</th>
                                <th>Constructive</th>
                                <th>Average Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(sub, index) in session.subsessions">
                                <td>
                                    <strong>{{sub.title}}</strong><br>
                                    {{sub.name}}
                                </td>
                                <td>
                                    <span v-for="item in sub.positive">{{item}}<br></span>
                                </td>
                                <td>
                                    <span v-for="item in sub.negative">{{item}}<br></span>
                                </td>
                                <td>
                                    {{sub.score}}/100
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                Score guide:
                    <ul>
                        <li>>95: an overwhelmingly excellent session, couldn't be improved</li>
                        <li>>80: an excellent sesssion, minimal grounds for improvement</li>
                        <li>>70: a very good session, minor points for improvement</li>
                        <li>>60: a fairly good session, could be improved further</li>
                        <li>>40: basically sound, but needs further development</li>
                        <li>>=20: not adequate in its current state</li>
                        <li>&#60;20: an extremely poor session</li>
                    </ul>
            </section>
            <section v-if="show.attendanceReport" id="attendanceReport" v-cloak>
                <div class="card p-2 bg-info" id="enterPinCard">
                    <h4 class="card-title">View attendance reports</h4>
                    <p class="card-text">You will need the pin from your confirmation email.</p>
                        <br>
                        <form method="post" action="api-v3/viewAttendancePDF.php">
                            <input class="form-control" type="text" name="id" placeholder="Session ID" v-model="session.id">
                            <input class="form-control" type="password" name="pin" placeholder="Session PIN">
                            <select name="format" class="form-control">
                                <option value="PDF">PDF</option>
                                <option value="CSV">CSV (Opens in Excel)</option>
                            </select>
                            <br>
                            <div class="text-center">
                                <button class="btn btn-primary" id="">Download attendance report</button>
                            </div>
                            <div>Lost your PIN? <a href='#' v-on:click="resetPIN">Click to reset.</a></div>
                        </form>
                </div>
                <br>
            </section>
            <section v-if="show.giveFeedback" id="giveFeedback" v-cloak>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    If you leave this page without submitting your feedback, LearnLoop will save your progress. Return to this form on the same device within the next 24 hours to pick up where you left off. <a href="/privacy-policy" class="alert-link">Click here to read about how LearnLoop uses cookies.</a>
                </div>
                <br>Please provide some feedback to <strong>{{session.name}}</strong> regarding their session <strong>'{{session.title}}'</strong> delivered on <strong>{{session.date}}</strong>.<br><br>
                <form id="giveFeedbackForm" class="needs-validation" novalidate>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Positive Comments:</span>
                            </div>
                            <textarea rows="8" v-model="session.feedback.positive" class="form-control" id="positiveComments" placeholder="Please provide some feedback about what you enjoyed about this session..." name="positiveComments" autocomplete="off" required></textarea>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                        <div v-if="session.tags">
                            <br>
                            Feedback quick tags (beta)<br>
                            <span v-for="(tag, index) in tags.positive">
                                <input type="checkbox" :id="'p-'+index" :value="index" v-model="session.feedback.tags.positive">
                                <label :for="'p-'+index">{{tag}}&nbsp;&nbsp;&nbsp;</label>
                            </span>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Constructive Comments:</span>
                            </div>
                            <textarea rows="8" v-model="session.feedback.negative" class="form-control" id="constructiveComments" placeholder="Please provide some feedback about ways this session could be improved..." name="constructiveComments" autocomplete="off" required></textarea>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                        <div v-if="session.tags">
                            <br>
                            Feedback quick tags (beta)<br>
                            <span v-for="(tag, index) in tags.negative">
                                <input type="checkbox" :id="'p-'+index" :value="index" v-model="session.feedback.tags.negative">
                                <label :for="'p-'+index">{{tag}}&nbsp;&nbsp;&nbsp;</label>
                            </span>
                        </div>
                        <br>
                        <div v-for="(question, index) in session.questions">
                            <h5>{{question.title}}</h5>
                            <div v-if="question.type == 'text'">
                                <textarea rows="8" v-model="question.response" class="form-control" id="question.title" name="question.title" autocomplete="off" required></textarea>
                            </div>
                            <div v-if="question.type == 'checkbox'">
                                <span v-for="(option, index) in question.options">
                                    <label>
                                        <input type="checkbox" value="option.title" v-model="option.selected">
                                        {{option.title}}
                                    </label>
                                    <br>
                                </span>
                            </div>
                            <div v-if="question.type == 'select'">
                                <select v-model="question.response" class="form-control" required>
                                    <option disabled value="">Please select one</option>
                                    <option v-for="(option, index) in question.options">{{option.title}}</option>
                                </select>
                                <div class="invalid-feedback">Please fill out this field.</div>
                            </div>
                            <br>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Overall score ({{session.feedback.score}}/100):</span>
                            </div>
                            <input type="range" v-model="session.feedback.score" style="width:80%; margin:10px;" id="scoreRange" placeholder="" name="scoreRange" autocomplete="off" oninput="app.scoreChange()" onchange="app.scoreChange()">
                            <input type="text" v-model="session.feedback.score" class="form-control-range" id="score" placeholder="" name="score" autocomplete="off" required hidden>
                            <div class="invalid-feedback">Please indicate an overall score using the slider.</div>
                        </div>
                        <div class="input-group">
                            <textarea rows=2 v-model="session.feedback.scoreText" class="form-control" id="scoreText" placeholder="" name="scoreText" autocomplete="off" readonly></textarea>
                        </div>
                    </div>
                </form>
                <div class="text-center">
                    <button class="btn btn-primary" id="submitGiveFeedback" v-on:click="giveFeedback">Give Feedback</button>
                </div>
            </section>
            <section v-if="show.giveFeedbackSeries" id="giveFeedbackSeries" v-cloak>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    If you leave this page without submitting your feedback, LearnLoop will save your progress (click on ). Return to this form on the same device within the next 24 hours to pick up where you left off. <a href="/privacy-policy" class="alert-link">Click here to read about how LearnLoop uses cookies.</a>
                </div>
                <br><strong>Please provide some feedback for '{{session.title}}' on {{session.date}} organised by {{session.name}}.</strong><br>
                Click on the <button class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i></button> by each session to provide your feedback.<br>
                If you did not attend one or more of the sessions listed you can select <button class="btn btn-secondary" >Skip this session</button> after clicking on the <button class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i></button>.<br><br>
                <h4>Feedback on sessions</h4>
                <table class="table" id="subsessionTable">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sub, index) in session.subsessions">
                            <td><strong>{{sub.title}}</strong><br>{{sub.name}}</td>
                            <td>{{sub.state}}</td>
                            <td><button style="float:right" class="btn btn-secondary btn-sm" id="btnEditSubsessionFeedback" v-on:click="editSubsessionFeedback(index)"><i class="fas fa-pen"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <div class="modal" id="editSubsessionFeedback">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Feedback for '{{subsession.title}}' by {{subsession.name}}</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form id="giveFeedbackSubsessionForm" class="needs-validation" novalidate>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Positive Comments:</span>
                                            </div>
                                            <textarea rows="8" v-model="subsession.positive" class="form-control" id="positiveComments" placeholder="Please provide some feedback about what you enjoyed about this session..." name="positive" autocomplete="off" required></textarea>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Constructive Comments:</span>
                                            </div>
                                            <textarea rows="8" v-model="subsession.negative" class="form-control" id="negative" placeholder="Please provide some feedback about ways this session could be improved..." name="negative" autocomplete="off" required></textarea>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Overall score (<span id="subsessionScore"></span>/100):</span>
                                            </div>
                                            <input type="range" v-model="subsession.score" style="width:80%; margin:10px;" id="subsessionScoreRange" placeholder="" name="subsessionScoreRange" autocomplete="off" oninput="app.scoreChange(true)" onchange="app.scoreChange(true)">
                                            <div class="invalid-feedback">Please indicate an overall score using the slider.</div>
                                        </div>
                                        <div class="input-group">
                                            <textarea rows=2 v-model="subsession.scoreText" class="form-control" id="subsessionScoreText" placeholder="" name="subsessionScoreText" autocomplete="off" readonly></textarea>
                                        </div>
                                    </div>
                                </form>
                                <button class="btn btn-primary" id="submitGiveFeedbackSubsession" v-on:click="giveFeedbackSubsession">Give feedback</button>
                                <button class="btn btn-secondary" id="submitSkipFeedbackSubsession" v-on:click="skipFeedbackSubsession">Skip this session</button>
                                <a href="#" data-toggle="modal" data-target="#skipFeedbackSubsessionInfo"><i class="fas fa-question-circle fa-2x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal" id="skipFeedbackSubsessionInfo">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Skipping feedback for a subsession</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                If you did not attend a particular session within a series you can skip providing feedback by clicking the 'Skip this session' button.<br>
                                <br>
                                The feedback you submit for the other sessions will still be submitted as usual.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center"><button class="btn btn-secondary btn-sm" id="saveProgress" v-on:click="saveProgress(true)">Save progress</button></div>
                <br>
                <h4>Overall / feedback for organiser</h4>
                <form id="giveFeedbackSeriesForm" class="needs-validation" novalidate>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Positive Comments:</span>
                            </div>
                            <textarea rows="8" v-model="session.feedback.positive" class="form-control" id="positive" placeholder="Please provide some feedback about what you enjoyed about this session series..." name="positiveComments" autocomplete="off" required></textarea>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Constructive Comments:</span>
                            </div>
                            <textarea rows="8" v-model="session.feedback.negative" class="form-control" id="negative" placeholder="Please provide some feedback about ways this session series could be improved..." name="constructiveComments" autocomplete="off" required></textarea>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                        <div v-if="session.questions">
                            <br>
                            <h4>Additional questions</h4>
                            <div v-for="(question, index) in session.questions">
                                <h5>{{question.title}}</h5>
                                <div v-if="question.type == 'text'">
                                    <textarea rows="8" v-model="question.response" class="form-control" id="question.title" name="question.title" autocomplete="off" required></textarea>
                                </div>
                                <div v-if="question.type == 'checkbox'">
                                    <span v-for="(option, index) in question.options">
                                        <label>
                                            <input type="checkbox" value="option.title" v-model="option.selected">
                                            {{option.title}}
                                        </label>
                                        <br>
                                    </span>
                                </div>
                                <div v-if="question.type == 'select'">
                                    <select v-model="question.response" class="form-control" required>
                                        <option disabled value="">Please select one</option>
                                        <option v-for="(option, index) in question.options">{{option.title}}</option>
                                    </select>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                                <br>
                            </div>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Overall score ({{session.feedback.score}}/100):</span>
                            </div>
                            <input type="range" v-model="session.feedback.score" style="width:80%; margin:10px;" id="scoreRange" placeholder="" name="scoreRange" autocomplete="off" oninput="app.scoreChange()" onchange="app.scoreChange()">
                            <input type="text" v-model="session.feedback.score" class="form-control-range" id="score" placeholder="" name="score" autocomplete="off" required hidden>
                            <div class="invalid-feedback">Please indicate an overall score using the slider.</div>
                        </div>
                        <div class="input-group">
                            <textarea rows=2 v-model="session.feedback.scoreText" class="form-control" id="scoreText" placeholder="" name="scoreText" autocomplete="off" readonly></textarea>
                        </div>
                    </div>
                </form>
                <div class="text-center">
                    <button class="btn btn-primary" id="submitGiveFeedbackSeries" v-on:click="giveFeedbackSeries">Give Feedback</button>
                </div>
            </section>
            <section v-if="show.completedFeedback" id="completedFeedback" v-cloak>
                <br><br>
                <div class="card-deck" style="display:block">
                    <div class="card p-2 bg-success" id="giveFeedbackCard">
                        <h4 class="card-title">Thank you for providing feedback</h4>
                        <p class="card-text">Your anonymous feedback will be returned to the session facilitator.</p>
                        <div v-if="show.certificate">
                            The facilitator has provided a certificate for this session for you to download. Please fill in the details below to generate your certificate.<br>
                            <form id="getCertificateForm" class="needs-validation" method="post" action="api-v3/getCertificate.php" novalidate>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Name for certificate:</span>
                                        </div>
                                        <input type="text" v-model="certName" class="form-control" id="certName" placeholder="Enter your name as you would like it to appear on your certificate..." name="certName" autocomplete="off" required>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <input type="text" v-model="certID" class="form-control" id="id" name="id" autocomplete="off" required hidden>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Your organisation:</span>
                                        </div>
                                        <select class="form-control" id="certOrgSelect" v-model="certOrg" v-on:change="selectOrg">
                                            <option>Gloucestershire Hospitals</option>
                                            <option>Great Western Hospitals</option>
                                            <option>Northern Devon Hospital</option>
                                            <option>Plymouth Hospitals</option>
                                            <option>Royal Cornwall Hospitals</option>
                                            <option>Royal Devon and Exeter Hospitals</option>
                                            <option>Royal United Hospitals Bath</option>
                                            <option>South Devon Healthcare</option>
                                            <option>Taunton and Somerset</option>
                                            <option>University Hospitals Bristol</option>
                                            <option>Yeovil District Hospital</option>
                                            <option>Other...</option>
                                        </select>
                                        <input type="text" v-model="certOrg" class="form-control" id="certOrg" name="certOrg" Placeholder="Please enter your current organisation..." autocomplete="off" required hidden="true">
                                    </div>
                                </div>
                            </form>
                            <small><strong>These details are not linked to the feedback you have just submitted but may be recorded for tracking attendance trends.</strong></small><br>
                            <br>
                            <button class="btn btn-info" id="downloadCertificate" v-on:click="downloadCertificate">Download certificate</button>
                        </div>
                    </div>
                </div>
            </section>
            <section v-if="show.createSession" id="createSession" v-cloak>
                <br>
                <strong>You are currently requesting feedback for a single session.</strong><br>
                Do you want to collect feedback for a series of sessions?
                <button class="btn btn-info" id="sessionSeriesSwitch" v-on:click="hideComponent('createSession'); showComponent('createSessionSeries')">Switch to session series</button>
                <a href="#" data-toggle="modal" data-target="#sessionSeriesModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="sessionSeriesModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Collect feedback for multiple sessions (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                You are currently requesting feedback for a single session. Alternatively, you can create a session series where attendees can provide feedback for multiple sessions (for example, a teaching day with different presenters) using a single link.
                                <br><br>
                                As the organiser you will be able to view all feedback collected, individual presenters can view the feedback for just their session.
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <br><strong>Please provide details of the session you are requesting feedback for.</strong><br><br>
                <form id="createSessionForm" class="needs-validation" novalidate>
                    <div class="form-group">
                      <label for="name">Session title:</label>
                      <input type="text" v-model="session.title" class="form-control" id="title" placeholder="Please enter the title of the teaching session..." name="title" autocomplete="off" required>
                      <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="date">Session date:</label>
                        <input type="date" v-model="session.date" class="form-control" id="date" placeholder="dd/mm/yyyy" name="date" autocomplete="off" required>
                        <div class="invalid-feedback">Please select a date.</div>
                    </div>
                    <div class="form-group">
                        <label for="name">Facilitator name:</label>
                        <input type="text" v-model="session.name" class="form-control" id="name" placeholder="Please enter the name of the session facilitator..." name="name" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Facilitator email:</label>
                        <input type="email" v-model="session.email" class="form-control" id="email" placeholder="Please enter the email for feedback to be returned to..." name="email" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </form>
                <br>
                <span v-if="!session.hasQuestions">
                    Do you want to ask additional questions to your attendees? <button class="btn btn-info" id="enableQuestions" v-on:click="session.hasQuestions = true;">Enable custom questions</button>
                    <a href="#" data-toggle="modal" data-target="#enableQuestionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                    <div class="modal" id="enableQuestionsModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Custom questions (Optional)</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    If the standard feedback form doesn't cover everything you want to ask, you can add additional questions.
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                </span>
                <div v-if="session.hasQuestions">
                    <h2>Custom questions</h2>
                    <table class="table" id="questionsTable">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(question, index) in session.questions">
                                <td>{{question.title}}</td>
                                <td>{{question.type}}</td>
                                <td><button style="float:right" class="btn btn-danger btn-sm" id="btnRemoveQuestion" v-on:click="removeQuestion(index)"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button style="float:right;" class="btn btn-success btn-sm" id="btnAddQuestion" data-toggle="modal" data-target="#addQuestion" data-backdrop="static">Add <i class="fas fa-plus"></i></button>
                    <div class="modal" id="addQuestion">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add a custom question</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="createQuestionForm" class="needs-validation" novalidate>
                                        <div class="form-group">
                                            <label for="title">Question title:</label>
                                            <input type="text" v-model="question.title" class="form-control" id="questionTitle" placeholder="Please enter your question..." name="title" autocomplete="off" required>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Question type: <a href="#" data-toggle="modal" data-target="#questionTypeInfo"><i class="fas fa-question-circle fa-1x"></i></a></label>
                                            <select v-model="question.type"  id="questionType" class="form-control" required>
                                                <option disabled value="">Please select question type</option>
                                                <option value="text">Text</option>
                                                <option value="checkbox">Checkboxes</option>
                                                <option value="select">Drop-down select</option>
                                            </select>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <div class="form-group" v-if="question.type == 'select' || question.type == 'checkbox'">
                                            <label for="type">Question options: <a href="#" data-toggle="modal" data-target="#questionOptionsInfo"><i class="fas fa-question-circle fa-1x"></i></a></label>
                                            <input type="text" v-model="question.options" class="form-control" id="questionOptions" placeholder="Please enter the question options..." name="options" autocomplete="off" required>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                    </form>
                                    <br><br>
                                    <button class="btn btn-primary" id="submitCreateQuestion" v-on:click="createQuestion">Add question</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="questionTypeInfo">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Question type info</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    There are three question types:
                                    <ul>
                                        <li>Text - a free-text area for attendees to type into, similar to the positive or constructive comment inputs on the default form</li>
                                        <li>Checkboxes - a series of checkboxes which attendees can select from. Attendees can select as many options as apply, or none.</li>
                                        <li>Drop-down select - a series of options in a drop-down menu. Attendees must select only one option.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="questionOptionsInfo">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Question options info</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    Please enter each option separated by a semicolon <code>;</code><br>
                                    <br>
                                    e.g. <code>option 1;option 2; option 3</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                </div>

                <span v-if="session.certificate">
                    Attendees will be able to download a certificate for this session after providing feedback. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.certificate">
                    Attendees will not be able to download a certificate for this session. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleCertificate" v-on:click="toggleCertificate">Disable certificate</button>
                <a href="#" data-toggle="modal" data-target="#certificateOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="certificateOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Certificate of attendance (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default attendees of your session will be able to download a certificate of attendance after completing the feedback form. This is a good way of encouraging attendees to provide feedback.
                                <br><br>
                                You can disable the certificate if you prefer. Attendees will still be able to provide feedback but will not be given the option to download a certificate afterwards.
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <span v-if="session.notifications">
                    You will receive an email each time feedback is submitted. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.notifications">
                    You won't receive an email each time feedback is submitted. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleNotifications" v-on:click="toggleNotifications">Disable notifications</button>
                <a href="#" data-toggle="modal" data-target="#notificationsOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="notificationsOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Email notifications when feedback submitted (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default you will receive an email when feedback for your session is submitted. To avoid overloading your inbox, no further notifications are sent within 2 hours.<br><br> If you disable this you can still manually check using your session ID and PIN which are emailed to you once your session is created. You can also disable further notifications later, using a link in the notification email itself.
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <span v-if="session.attendance">
                    Register of attendance will be kept. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.attendance">
                    Register of attendance won't be kept. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleAttendance" v-on:click="toggleAttendance">Disable register of attendance</button>
                <a href="#" data-toggle="modal" data-target="#attendanceOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="attendanceOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Register of attendance (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default you will be able to generate an attendance report of people who have attended your session. The attendance report shows the name and organisation of each attendee who downloads a certificate of attendance. The attendee details are not linked to their feedback. To reduce the risk of attendees being linked to their feedback you will only be able to view a register of attendance once you have received at least 3 feedback submissions.<br><br>The certificate option must be enabled for the attendance register to be available.
                            </div>
                        </div>
                    </div>
                </div>
                <!--<br><br>
                <span v-if="session.tags">
                    Feedback quick tags will be available (beta). <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.tags">
                    Feedback quick tags won't be used. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleTags" v-on:click="toggleTags">Disable feedback quick tags</button>
                <a href="#" data-toggle="modal" data-target="#tagsOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="tagsOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Feedback quick tags (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default attendees will be able to use tags to quickly indicate positive or constructive points in addition to their free-text comments. For example, <i>"Try to reduce the amount of text on the slides"</i> or <i>"Good use of interactivity"</i>.<br><br> This function is in beta and may not work completely just yet.<br><br>You can disable feedback quick tags if you prefer to just gather free-text comments and a score.
                            </div>
                        </div>
                    </div>
                </div>-->
                <br><br>
                <div class="text-center">
                    <button class="btn btn-primary" id="submitCreateSession" v-on:click="createSession">Create session</button>
                </div>
            </section>
            <section v-if="show.createSessionSeries" id="createSessionSeries" v-cloak>
                <br>
                <strong>You are currently requesting feedback for a session series.</strong>
                <button class="btn btn-info" id="sessionSingleSwitch" v-on:click="hideComponent('createSessionSeries'); showComponent('createSession')">Switch back to single session</button>
                <br>
                <br><strong>Please provide details of the session series you are requesting feedback for.</strong><br><br>
                <form id="createSessionSeriesForm" class="needs-validation" novalidate>
                    <div class="form-group">
                      <label for="name">Session series title:</label>
                      <input type="text" v-model="session.title" class="form-control" id="title" placeholder="Please enter the title of the teaching day or series of sessions..." name="title" autocomplete="off" required>
                      <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="date">Session series date:</label>
                        <input type="date" v-model="session.date" class="form-control" id="date" placeholder="dd/mm/yyyy" name="date" autocomplete="off" required>
                        <div class="invalid-feedback">Please select a date.</div>
                    </div>
                    <div class="form-group">
                        <label for="name">Organiser name:</label>
                        <input type="text" v-model="session.name" class="form-control" id="name" placeholder="Please enter the name of the session organiser..." name="name" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Organiser email:</label>
                        <input type="email" v-model="session.email" class="form-control" id="email" placeholder="Please enter the email for feedback to be returned to..." name="email" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </form>
                <br>

                <h2>Sessions</h2>
                <table class="table" id="subsessionTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sub, index) in session.subsessions">
                            <td>{{sub.title}}</td>
                            <td>{{sub.name}}</td>
                            <td>{{sub.email}}</td>
                            <td><button style="float:right" class="btn btn-danger btn-sm" id="btnRemoveSubsession" v-on:click="removeSubsession(index)"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button style="float:right;" class="btn btn-success btn-sm" id="btnAddSubsession" data-toggle="modal" data-target="#addSubsession" data-backdrop="static">Add <i class="fas fa-plus"></i></button>
                <div class="modal" id="addSubsession">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Add a session to this series</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form id="createSubsessionForm" class="needs-validation" novalidate>
                                    <div class="form-group">
                                        <label for="name">Session title:</label>
                                        <input type="text" v-model="subsession.title" class="form-control" id="subsessionTitle" placeholder="Please enter the title this session..." name="title" autocomplete="off" required>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Facilitator name:</label>
                                        <input type="text" v-model="subsession.name" class="form-control" id="subsessionName" placeholder="Please enter the name of the session facilitator..." name="name" autocomplete="off" required>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Facilitator email: <a href="#" data-toggle="modal" data-target="#subsessionEmailInfo"><i class="fas fa-question-circle fa-1x"></i></a></label>
                                        <input type="email" v-model="subsession.email" class="form-control" id="subsessionEmail" placeholder="Please enter the email for feedback to be returned to..." name="email" autocomplete="off" required>
                                    </div>
                                </form>
                                <br><br>
                                <button class="btn btn-primary" id="submitCreateSubsession" v-on:click="createSubsession">Add to series</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal" id="subsessionEmailInfo">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Providing an email address for a session within a series</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                If you provide an email for the facilitator of a session within a series that you are organising, they will receive a email with details about how to view the feedback for just their session. They will also receive a notification each time an attendee submits feedback for their session, but they can disable these using a link in the email if they prefer.
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                
                <span v-if="!session.hasQuestions">
                    Do you want to ask additional questions to your attendees? <button class="btn btn-info" id="enableQuestions" v-on:click="session.hasQuestions = true;">Enable custom questions</button>
                    <a href="#" data-toggle="modal" data-target="#enableQuestionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                    <div class="modal" id="enableQuestionsModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Custom questions (Optional)</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    If the standard feedback form doesn't cover everything you want to ask, you can add additional questions.
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                </span>
                <div v-if="session.hasQuestions">
                    <h2>Custom questions</h2>
                    <table class="table" id="questionsTable">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(question, index) in session.questions">
                                <td>{{question.title}}</td>
                                <td>{{question.type}}</td>
                                <td><button style="float:right" class="btn btn-danger btn-sm" id="btnRemoveQuestion" v-on:click="removeQuestion(index)"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button style="float:right;" class="btn btn-success btn-sm" id="btnAddQuestion" data-toggle="modal" data-target="#addQuestion" data-backdrop="static">Add <i class="fas fa-plus"></i></button>
                    <div class="modal" id="addQuestion">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add a custom question</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="createQuestionForm" class="needs-validation" novalidate>
                                        <div class="form-group">
                                            <label for="title">Question title:</label>
                                            <input type="text" v-model="question.title" class="form-control" id="questionTitle" placeholder="Please enter your question..." name="title" autocomplete="off" required>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Question type: <a href="#" data-toggle="modal" data-target="#questionTypeInfo"><i class="fas fa-question-circle fa-1x"></i></a></label>
                                            <select v-model="question.type"  id="questionType" class="form-control" required>
                                                <option disabled value="">Please select question type</option>
                                                <option value="text">Text</option>
                                                <option value="checkbox">Checkboxes</option>
                                                <option value="select">Drop-down select</option>
                                            </select>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                        <div class="form-group" v-if="question.type == 'select' || question.type == 'checkbox'">
                                            <label for="type">Question options: <a href="#" data-toggle="modal" data-target="#questionOptionsInfo"><i class="fas fa-question-circle fa-1x"></i></a></label>
                                            <input type="text" v-model="question.options" class="form-control" id="questionOptions" placeholder="Please enter the question options..." name="options" autocomplete="off" required>
                                            <div class="invalid-feedback">Please fill out this field.</div>
                                        </div>
                                    </form>
                                    <br><br>
                                    <button class="btn btn-primary" id="submitCreateQuestion" v-on:click="createQuestion">Add question</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="questionTypeInfo">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Question type info</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    There are three question types:
                                    <ul>
                                        <li>Text - a free-text area for attendees to type into, similar to the positive or constructive comment inputs on the default form</li>
                                        <li>Checkboxes - a series of checkboxes which attendees can select from. Attendees can select as many options as apply, or none.</li>
                                        <li>Drop-down select - a series of options in a drop-down menu. Attendees must select only one option.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="questionOptionsInfo">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Question options info</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    Please enter each option separated by a semicolon <code>;</code><br>
                                    <br>
                                    e.g. <code>option 1;option 2; option 3</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                </div>

                <span v-if="session.certificate">
                    Attendees will be able to download a certificate for this session series after providing feedback. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.certificate">
                    Attendees will not be able to download a certificate for this session series. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleCertificate" v-on:click="toggleCertificate">Disable certificate</button>
                <a href="#" data-toggle="modal" data-target="#certificateOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="certificateOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Certificate of attendance (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default attendees of your teaching day or session series will be able to download a certificate of attendance after completing the feedback form. This is a good way of encouraging attendees to provide feedback.
                                <br><br>
                                You can disable the certificate if you prefer. Attendees will still be able to provide feedback but will not be given the option to download a certificate afterwards.
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <span v-if="session.notifications">
                    You will receive an email each time feedback is submitted. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.notifications">
                    You won't receive an email each time feedback is submitted. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleNotifications" v-on:click="toggleNotifications">Disable notifications</button>
                <a href="#" data-toggle="modal" data-target="#notificationsOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="notificationsOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Email notifications when feedback submitted (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default you will receive an email when feedback for your session is submitted. To avoid overloading your inbox, no further notifications are sent within 2 hours.<br><br> If you disable this you can still manually check using your session ID and PIN which are emailed to you once your session is created. You can also disable further notifications later, using a link in the notification email itself.
                                <br><br>
                                If you provide an email for them, facilitators of each session in this series will receive an email notifying them that the feedback request has been set up. They will also receive email notifications when feedback for their session is submitted, but they can disable these if preferred using a link in the notification email itself.
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <span v-if="session.attendance">
                    Register of attendance will be kept. <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.attendance">
                    Register of attendance won't be kept. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleAttendance" v-on:click="toggleAttendance">Disable register of attendance</button>
                <a href="#" data-toggle="modal" data-target="#attendanceOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="attendanceOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Register of attendance (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default you will be able to generate an attendance report of people who have attended your session. The attendance report shows the name and organisation of each attendee who downloads a certificate of attendance. The attendee details are not linked to their feedback. To reduce the risk of attendees being linked to their feedback you will only be able to view a register of attendance once you have received at least 3 feedback submissions.<br><br>The certificate option must be enabled for the attendance register to be available.
                            </div>
                        </div>
                    </div>
                </div>
                <!--<br><br>
                <span v-if="session.tags">
                    Feedback quick tags will be available (beta). <i style="color:green" class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span v-if="!session.tags">
                    Feedback quick tags won't be used. <i style="color:red"class="fas fa-times"></i>
                </span>
                <button class="btn btn-info" id="toggleTags" v-on:click="toggleTags">Disable feedback quick tags</button>
                <a href="#" data-toggle="modal" data-target="#tagsOptionsModal"><i class="fas fa-question-circle fa-2x"></i></a>
                <div class="modal" id="tagsOptionsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Feedback quick tags (Optional)</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                By default attendees will be able to use tags to quickly indicate positive or constructive points in addition to their free-text comments. For example, <i>"Try to reduce the amount of text on the slides"</i> or <i>"Good use of interactivity"</i>.<br><br> This function is in beta and may not work completely just yet.<br><br>You can disable feedback quick tags if you prefer to just gather free-text comments and a score.
                            </div>
                        </div>
                    </div>
                </div>-->
                <br><br>
                <div class="text-center">
                    <button class="btn btn-primary" id="submitCreateSessionSeries" v-on:click="createSessionSeries">Create session series</button>
                </div>
            </section>
            <section v-if="show.requestFeedback" id="requestFeedback" v-cloak>
                <br><br>
                <div class="card-deck" style="display:block">
                    <div class="card p-2 bg-success" id="giveFeedbackCard">
                        <h4 class="card-title">Your session was created successfully</h4>
                        <br>
                        <strong>Please check your inbox to ensure you received the confirmation email.</strong>
                        If it didn't arrive, take a look in your junk mail, or be sure to make a note of the details below. Add mail@learnloop.co.uk your safe senders list for next time.<br>
                        <br>
                        <h5>How to view your feedback</h5>
                        <div>Go to <a :href=invite.view><strong>{{invite.view}}</strong></a> and enter your pin: <strong>{{session.pin}}</strong> to retrieve submitted feedback (do not share your pin with attendees).</div><br>
                        <br>
                        <h5>How to direct attendees to the feedback form</h5>
                        <p>You can copy the full feedback invitation into an email to send to your attendees, or share just the link, or you can use the QR code below.</p>
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
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">QR Code:&nbsp;&nbsp;</span>
                                </div>
                                <div id="inviteQR" class="copyFrom" style="width:80%">
                                    <img :src="invite.qr" alt="QR code" id="imgQR" height="250">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            <div id="logger"></div>
            <div id="spacer"></div>
        </div>
        <?php include "footer.php";?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!--add to dependencies-->
    <script src="app-v3.js"></script>
</body>
</html>


