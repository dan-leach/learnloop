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
            <section v-if="show.completedFeedback" id="completedFeedback" v-cloak>
                <br><br>
                <div class="card-deck" style="display:block">
                    <div class="card p-2 bg-success" id="giveFeedbackCard">
                        <h4 class="card-title">Thank you for providing feedback</h4>
                        <p class="card-text">Your anonymous feedback will be returned to the session facilitator.</p>
                        <div v-if="session.certificate">
                            The facilitator has provided a certificate for this session for you to download. Please fill in the details below to generate your certificate.<br>
                            <form id="getCertificateForm" class="needs-validation" method="post" action="api-v3/certificate-fix.php" novalidate>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Name for certificate:</span>
                                        </div>
                                        <input type="text" v-model="certName" class="form-control" id="certName" placeholder="Enter your name as you would like it to appear on your certificate..." name="certName" autocomplete="off" required>
                                        <div class="invalid-feedback">Please fill out this field.</div>
                                    </div>
                                    <input type="text" v-model="session.id" class="form-control" id="id" name="id" autocomplete="off" required hidden>
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
            <div id="logger"></div>
            <div id="spacer"></div>
        </div>
        <?php include "footer.php";?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!--add to dependencies-->
    <script src="app-certificate-fix.js"></script>
</body>
</html>


