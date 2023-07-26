<!DOCTYPE html>
<html>
<head>
    <title>LearnLoop</title>

    <?php include "../assets/dependencies.php";?>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm bg-info navbar-light d-flex justify-content-between">
            <a class="navbar-brand" href="/"><img src="/assets/img/logo.png" alt="LearnLoop Logo" height="50"></a>
            <h2>DEVELOPMENT VERSION</h2>
            <ul class="nav nav-pills nav-justified">
                <li class="nav-item">
                    <a class="nav-link" href="/feedback">Feedback</a>
                </li>
                <li class="nav-item">
                    <p class="nav-link active">Interact</p>
                </li>
            </ul>
        </nav>
        <div class="container">
            <h1 class="display-4 text-center">Interact</h1>
            <section v-if="show.welcome" id="welcome" v-cloak>
                <p class="text-center">Engage your audience with live interactions.</p>
                <div class="card-deck">
                    <div class="card p-2 bg-info" id="joinSessionCard">
                        <h4 class="card-title">Join session</h4>
                        <p class="card-text">The session facilitator will have provided you with a session ID code. Enter the code in the search box below, then click 'Join session'.
                            <br><br>
                            <div class="input-group">
                                <input v-model="session.id" type="text" class="form-control" placeholder="Session ID" autocomplete="off">
                                <button type="button" id="joinSession" class="btn btn-primary" v-on:click="joinSession">Join session</button>
                            </div>
                        </p>
                    </div>
                    <div class="card p-2 bg-info" id="createSessionCard">
                        <h4 class="card-title">Create session</h4>
                        <p class="card-text">To set up a new interact session, and generate a link or QR code to share with attendees, click 'Create new session'.
                            <br><br>
                            <div class="text-center">
                                <button class="btn btn-primary" id="startCreateSession" v-on:click="hideComponent('welcome'); showComponent('createSession')">Create new session</button>
                            </div>
                        </p>
                    </div>
                    <div class="card p-2 bg-info" id="otherOptionsCard">
                        <h4 class="card-title">More options</h4>
                        <p class="card-text">Select the option you require.
                            <br>
                            <div class="text-center">
                                <button type="button" id="loadPinUpdateDetails" class="btn btn-primary m-1" v-on:click="loadPinUpdateDetails">Edit session</button>
                                <button type="button" id="loadPinViewSubmissions" class="btn btn-primary m-1" v-on:click="loadPinViewSubmissions">View submissions</button>
                                <button type="button" id="closeSession" class="btn btn-primary m-1" v-on:click="closeSession">Close session</button>
                                <button type="button" id="resetPIN" class="btn btn-primary m-1" v-on:click="resetPIN">Reset PIN</button>
                                <button type="button" id="findMySessions" class="btn btn-primary m-1" v-on:click="findMySessions">Find my sessions</button>
                            </div>
                        </p>
                    </div>
                </div>
                <br>
                <div class="quote" v-html="quote"></div>
                <br>
                <div class="card p-2 bg-light" id="updateInfo" hidden>
                    Update July 2023:
                    <ul>
                        <li>Update info...</li>
                    </ul>
                </div>
            </section>
            <section v-if="show.loader" id="loader">
                <br><br>
                <div class="spinner-border"></div>    
                Please wait... LearnLoop is loading
            </section>
            <!--session creation-->
            <section v-if="show.createSession" id="createSession" v-cloak>
                <br>
                <br><strong>Please provide details of the session you are creating.</strong><br><br>
                <form id="createSessionForm" class="needs-validation" novalidate>
                    <div class="form-group">
                      <label for="name">Session title:</label>
                      <input type="text" v-model="session.title" class="form-control" id="title" placeholder="Title for session..." name="title" autocomplete="off" required>
                      <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="name">Facilitator name:</label>
                        <input type="text" v-model="session.name" class="form-control" id="name" placeholder="Facilitator delivering the session..." name="name" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Facilitator email:</label>
                        <input type="email" v-model="session.email" class="form-control" id="email" placeholder="Email to send session details..." name="email" autocomplete="off" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </form>
                <br>
                <h2>Questions</h2>
                <table class="table" id="questionsTable">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Type</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(question, index) in session.questions">
                            <td>{{question.title}}</td>
                            <td>{{question.type}}</td>
                            <td><button style="float:right" class="btn btn-secondary btn-sm" id="btnEditQuestion" v-on:click="editQuestion(index)"><i class="fas fa-edit"></i></button></td>
                            <td><button style="float:right" class="btn btn-danger btn-sm" id="btnRemoveQuestion" v-on:click="removeQuestion(index)"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button style="float:right;" class="btn btn-success btn-sm" id="btnAddQuestion" data-toggle="modal" data-target="#addQuestion" data-backdrop="static">Add <i class="fas fa-plus"></i></button>
                <div class="modal" id="addQuestion">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Add a question</h4>
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
                <div class="text-center">
                    <button class="btn btn-primary" id="submitCreateSession" v-on:click="createSession">Create session</button>
                </div>
            </section>
            <section v-if="show.sessionCreated" id="sessionCreated" v-cloak>
                <br><br>
                <div class="card-deck" style="display:block">
                    <div class="card p-2" id="sessionCreatedCard">
                        <h5 class="card-title">Your session was created successfully</h5>
                        <br>
                        <span style='font-size:2em'>
                            Your session ID is <strong>{{session.id}}</strong><br>
                            Your session PIN is <strong>{{session.pin}}</strong>
                        </span><br>
                        Please check your inbox to ensure you received the confirmation email. If it didn't arrive, take a look in your junk mail, or be sure to make a note of these details. Add mail@learnloop.co.uk your safe senders list for next time.<br>
                        <br>
                        <h5>How to direct attendees to join the session</h5>
                        <p>From the options below you can copy the session link, or download the QR code to embed into a presentation.</p>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Session link:&nbsp;&nbsp;</span>
                                </div>
                                <div id="inviteLink" v-html="invite.link" class="copyFrom"></div>
                                <div class="input-group-append">
                                    <button type="button" id="copyLink" class="btn btn-primary" v-on:click="copy('inviteLink')" v-if="showCopyBtns">Copy</button>
                                </div>
                            </div>
                            <br>
                            Insert this QR code into a slideshow presentation to allow your attendees to link directly to the session from using their smartphones. Right click on the QR code image and select "Save image as...", then insert the saved image into your presentation.<br>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">QR Code:&nbsp;&nbsp;</span>
                                </div>
                                <div id="inviteQR" class="copyFrom" style="width:80%">
                                    <img :src="invite.qr" alt="QR code" id="imgQR" height="250">
                                </div>
                            </div>
                        </div>
                        <h5>How to view submissions during the event</h5>
                        <div>Go to <a :href=invite.view><strong>{{invite.view}}</strong></a> and enter your pin: <strong>{{session.pin}}</strong> to view submissions (do not share your pin with attendees).</div><br>
                        <br>
                    </div>
                </div>
            </section>
            <!--join-->
            <section v-if="show.join" id="join" v-cloak>
                <br>Interact session: <strong>'{{session.title}}'</strong> by <strong>{{session.name}}</strong>.<br><br>
                <div v-for="(question, index) in session.questions">
                    <span v-if="index == session.currentQuestionIndex">
                        <form id="giveSubmission" class="needs-validation" novalidate>
                            <div class="form-group">
                                <h5>{{index+1}}: {{question.title}}</h5>
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
                        </form>
                        <div class="text-center">
                            <button class="btn btn-primary" id="insertSubmission" v-on:click="insertSubmission(index)">Submit</button>
                        </div>
                        <div class="text-center">
                            question:{{index+1}} of {{session.questions.length}} [index:{{index}}]<br>
                            <button v-if="session.currentQuestionIndex != 0" class="btn btn-primary" id="previousQuestion" v-on:click="session.currentQuestionIndex--">Previous Question</button>
                            <button v-if="session.currentQuestionIndex != session.questions.length-1" class="btn btn-primary" id="nextQuestion" v-on:click="session.currentQuestionIndex++">Next Question</button>
                        </div>
                    </span>
                </div>
            </section>
            <!--view-->
            <section v-if="show.view" id="view" v-cloak>
                <br>Interact session: <strong>'{{session.title}}'</strong> by <strong>{{session.name}}</strong>.<br><br>
                <div v-for="(question, index) in session.questions">
                    <span v-if="index == session.currentQuestionIndex">
                        <form id="viewSubmission" class="needs-validation" novalidate>
                            <div class="form-group">
                                <h5>{{index+1}}: {{question.title}}</h5>
                                <div v-if="question.type == 'text'">
                                    <span v-for="response in question.responses">{{response}}<br></span>
                                </div>
                                <div v-if="question.type == 'checkbox' || question.type == 'select'">
                                    <span v-for="(option, index) in question.options">
                                        {{option.title}}: {{option.count}}
                                        <br>
                                    </span>
                                </div>
                                <br>
                            </div>
                        </form>
                        <div class="text-center">
                            question:{{index+1}} of {{session.questions.length}} [index:{{index}}]<br>
                            <button v-if="session.currentQuestionIndex != 0" class="btn btn-primary" id="previousQuestion" v-on:click="session.currentQuestionIndex--">Previous Question</button>
                            <button v-if="session.currentQuestionIndex != session.questions.length-1" class="btn btn-primary" id="nextQuestion" v-on:click="session.currentQuestionIndex++">Next Question</button>
                        </div>
                    </span>
                </div>
            </section>
            <div id="logger"></div>
            <div id="spacer"></div>
        </div>
        <?php include '../assets/footer.php'?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!--add to dependencies-->
    <script src="interact-app-v1.js"></script>
</body>
</html>


