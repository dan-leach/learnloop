console.log ("app-v3.js running...")
var data = {
  viewID: '',
  certName: '',
  certOrg: '',
  session: {
    id: '',
    name: '',
    email: '',
    title: '',
    date: '',
    certificate: true,
    subsessions: [],
    notifications: true,
    pin: '',
    feedback: {
      positive: [],
      negative: [],
      score: null,
      scoreText: 'Please indicate an overall score using the slider.'
    }
  },
  subsession: {
    name: '',
    email: '',
    title: '',
    feedback: {
      positive: [],
      negative: [],
      score: null,
      scoreText: '',
      index: 0,
      state: "To do"
    }
  },
  feedback: {
    positiveComments: '',
    constructiveComments: '',
    score: null,
    scoreText: 'Please indicate an overall score using the slider.'
  },
  show: {
    loader: true,
    welcome: false,
    enterPin: false,
    viewFeedback: false,
    giveFeedback: false,
    giveFeedbackSeries: false,
    completedFeedback: false,
    createSession: false,
    createSessionSeries: false,
    requestFeedback: false
  },
  invite: {
    full: '',
    link: '',
    qr: ''
  },
  showCopyBtns: true,
  quote: '',
  quoteList: [
        {
            quote: "We all need people who will give us feedback. That’s how we improve.",
            cite: "Bill Gates"
        },
        {
            quote: "Criticism, like rain, should be gentle enough to nourish a man’s growth without destroying his roots.",
            cite: "Frank A. Clark"
        },
        {
            quote: "Feedback is the breakfast of champions.",
            cite: "Ken Blanchard"
        },
        {
            quote: "Feedback is a gift. Ideas are the currency of our next success. Let people see you value both feedback and ideas.",
            cite: "Jim Trinka and Les Wallace"
        },
        {
          quote: "There is no failure. Only feedback.",
          cite: "Robert Allen"
        },
        {
          quote: "No matter how good you think you are as a leader, my goodness, the people around you will have all kinds of ideas for how you can get better. So for me, the most fundamental thing about leadership is to have the humility to continue to get feedback and to try to get better – because your job is to try to help everybody else get better.",
          cite: "Jim Yong Kim"
        },
        {
          quote: "True intuitive expertise is learned from prolonged experience with good feedback on mistakes.",
          cite: "Daniel Kahneman"
        },
        {
          quote: "Criticism may not be agreeable, but it is necessary. It fulfils the same function as pain in the human body. It calls attention to an unhealthy state of things.",
          cite: "Winston Churchill"
        },
        {
          quote: "All that is valuable in human society depends upon the opportunity for development accorded the individual.",
          cite: "Albert Einstein"
        },
        {
          quote: "Strive for continuous improvement, instead of perfection.",
          cite: "Kim Collins"
        },
        {
          quote: "Never be afraid to fail. Failure is only a stepping stone to improvement.",
          cite: "Tony Jaa"
        },
        {
          quote: "There is always space for improvement, no matter how long you’ve been in the business.",
          cite: "Oscar De La Hoya"
        },
        {
          quote: "Knowledge rests not upon truth alone, but upon error also.",
          cite: "Carl Jung"
        },
        {
          quote: "To become more effective and fulfilled at work, people need a keen understanding of their impact on others and the extent to which they’re achieving their goals in their working relationships. Direct feedback is the most efficient way for them to gather this information and learn from it.",
          cite: "Ed Batista"
        },
        {
          quote: "We can’t just sit back and wait for feedback to be offered, particularly when we’re in a leadership role. If we want feedback to take root in the culture, we need to explicitly ask for it.",
          cite: "Ed Batista"
        },
        {
          quote: "Average players want to be left alone. Good players want to be coached. Great players want to be told the truth.",
          cite: "Doc Rivers"
        }
    ]
}

var app = new Vue({
    el: '#app',
    data: data,
    methods: {
      getSession: function(){
        console.log("Start getSession...")
        if (data.session.id == '') return
        $( "#getExistingSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#getExistingSession" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              if (res.redirect == "v2") {
                console.log("Redirecting to v2")
                window.location.replace("https://learnloop.co.uk/v2.php?"+data.session.id);
                return
              }
              console.log('Session data retrieved: ', res)
              data.session.title = res.title
              data.session.date = res.date
              data.session.name = res.name
              data.session.subsessions = res.subsessions
              data.session.certificate = (res.certificate == true) ? true : false;
              if (data.session.subsessions) {
                for (let sub of data.session.subsessions) sub.state = "To do"
                app.hideComponent('loader')
                app.hideComponent('welcome')
                app.showComponent('completedFeedback')
              } else {
                app.hideComponent('loader')
                app.hideComponent('welcome')
                app.showComponent('completedFeedback')
              }
            } catch (e) {
              console.log(e)
              data.session.id = ''
              app.hideComponent('loader')
              app.showComponent('welcome')
              $( "#getExistingSession" ).html("Give feedback")
              $( "#getExistingSession" ).prop( "disabled", false );
              Swal.fire({
                  icon: 'error',
                  text: this.responseText
              })
            }
          }
        };
        xhttp.open("GET", "api-v3/getSession.php?id="+data.session.id, true)
        xhttp.send()
      },
      enterPIN: function(){
        console.log("Start enterPIN...")
        if (data.viewID == '') return
        data.session.id = data.viewID
        this.hideComponent('welcome')
        this.showComponent('enterPin')
      },
      viewFeedback: function(){
        console.log("Start viewFeedback...")
        $( "#submitPin" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitPin" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              try {
                var res = JSON.parse(this.responseText)
                console.log(res)
                data.session.name = res.name
                data.session.title = res.title
                data.session.date = res.date
                data.session.feedback.positive = (res.positive)
                data.session.feedback.negative = (res.negative)
                let sum = 0
                for (let score of res.score) sum += parseInt(score)
                data.session.feedback.score = Math.round((sum/res.score.length)*10)/10
                if (Number.isNaN(data.session.feedback.score)) data.session.feedback.score = "-"
                data.session.subsessions = res.subsessions
                if (res.subsessions) {
                  for (let sub of res.subsessions) {
                    let sum = 0
                    for (let score of sub.score) sum += parseInt(score)
                    sub.score = Math.round((sum/sub.score.length)*10)/10
                    if (Number.isNaN(sub.score)) sub.score = "-"
                  }
                }
                app.hideComponent('enterPin')
                app.showComponent('viewFeedback')

              } catch (e) {
                console.log(e)
                Swal.fire({
                  icon: 'error',
                  text: this.responseText
                })
                $( "#submitPin" ).html("Retry view feedback?")
                $( "#submitPin" ).prop( "disabled", false )
              }            
            }
          };
          xhttp.open("POST", "api-v3/viewFeedback.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.send("id="+data.session.id+"&pin="+data.session.pin);
      },
      createSession: function(){
        console.log("Start createSession...")
        $( "#createSessionForm" ).addClass("was-validated")
        if (data.session.title == '' || data.session.date == '' || data.session.name == '' || data.session.email == '') return
        $( "#submitCreateSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSession" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              data.session.id = res.id
              data.session.pin = res.pin
              data.session.date = res.date
              var tempLink = 'https://learnloop.co.uk?' + data.session.id
              data.invite.full = "Dear attendee,<br><br>You attended my session <strong>'" + data.session.title + "'</strong> on " + data.session.date + ". I would be grateful if you would take the time to provide some anonymous feedback for me using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://learnloop.co.uk'>learnloop.co.uk</a></strong> and enter the session ID code: <strong>" + data.session.id + "</strong><br><br>Many thanks,<br>" + data.session.name + "<br><br><i>You can request feedback for your own sessions too using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>"
              data.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
              data.invite.view = 'https://learnloop.co.uk?view=' + data.session.id
              data.invite.qr = 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8'
              app.hideComponent('createSession')
              app.showComponent('requestFeedback')
            } catch (e) {
              console.log(e)
              Swal.fire({
                icon: 'error',
                text: this.responseText
              })
              $( "#submitCreateSession" ).html("Retry create session?")
              $( "#submitCreateSession" ).prop( "disabled", false )
            }            
          }
        };
        xhttp.open("POST", "api-v3/createSession.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("session="+JSON.stringify(data.session));
      },
      createSessionSeries: function(){
        console.log("Start createSessionSeries...")
        $( "#createSessionSeriesForm" ).addClass("was-validated") // add validation formatting to form
        if (data.session.title == '' || data.session.date == '' || data.session.name == '' || data.session.email == '') return //exit if any fields blank
        if (data.session.subsessions.length == 0) { //alert and exit if no subsessions
          Swal.fire({
            icon: 'error',
            text: 'You need to add at least one session to this session series.'
          })
          return
        }

        $( "#submitCreateSessionSeries" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSessionSeries" ).prop( "disabled", true );

        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            console.log (this.responseText)
            try {
              var res = JSON.parse(this.responseText)
              data.session.id = res.id
              data.session.pin = res.pin
              data.session.date = res.date
              var tempLink = 'https://learnloop.co.uk?' + data.session.id
              data.invite.full = "Dear attendee,<br><br>You attended the session series <strong>'" + data.session.title + "'</strong> on " + data.session.date + ". I would be grateful if you would take the time to provide some anonymous feedback for the sessions using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://learnloop.co.uk'>learnloop.co.uk</a></strong> and enter the session ID code: <strong>" + data.session.id + "</strong><br><br>Many thanks,<br>" + data.session.name + "<br><br><i>You can request feedback for your own sessions too using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>"
              data.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
              data.invite.view = 'https://learnloop.co.uk?view=' + data.session.id
              data.invite.qr = 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8'
              app.hideComponent('createSessionSeries')
              app.showComponent('requestFeedback')
            } catch (e) {
              console.log(e)
              Swal.fire({
                icon: 'error',
                text: this.responseText
              })
              $( "#submitCreateSessionSeries" ).html("Retry create session?")
              $( "#submitCreateSessionSeries" ).prop( "disabled", false )
            }
          }
        };
        xhttp.open("POST", "api-v3/createSessionSeries.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("session="+JSON.stringify(data.session))
      },
      createSubsession: function(){
        $( "#createSubsessionForm" ).addClass("was-validated") //add validation formatting to form
        if (data.subsession.title == '' || data.subsession.name == '' || data.subsession.email == '') return //exit validation if any fields blank
        
        $( "#submitCreateSubsession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSubsession" ).prop( "disabled", true );
        
        //api call to check email is valid
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              if (!res.emailFormatIsValid) { //alert and exit if email not valid
                Swal.fire({text: "Please check email and try again.", icon: 'error'})
                  $("#submitCreateSubsession" ).html("Retry add to series?")
                  $("#submitCreateSubsession" ).prop("disabled", false )
                  return
              }

              data.session.subsessions.push({...data.subsession})
              
              //reset form ready for next subsession add
              data.subsession.title = ''
              data.subsession.name = ''
              data.subsession.email = ''
              $( "#createSubsessionForm" ).removeClass("was-validated")
              $("#submitCreateSubsession" ).html("Add to series")
              $("#submitCreateSubsession" ).prop("disabled", false )
              $('#addSubsession').modal('hide');

            } catch (e) {
              console.error(e)
              $("#submitCreateSubsession" ).html("Retry add to series?")
              $("#submitCreateSubsession" ).prop("disabled", false )
            }            
          }
        };
        xhttp.open("POST", "api-v3/emailFormatIsValid.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("email="+data.subsession.email);
      },
      removeSubsession: function(i){
        console.log("Start removeSubsession...")
        Swal.fire({
          title: 'Remove this session?',
          showCancelButton: true,
          confirmButtonText: 'Yes',
        }).then((result) => {
          if (result.isConfirmed) {
            data.session.subsessions.splice(i,1)
          }
        })
      },
      editSubsessionFeedback: function(i){
        console.log("Start editSubsessionFeedback...")
        
        data.subsession.name = data.session.subsessions[i].name
        data.subsession.title = data.session.subsessions[i].title
        data.subsession.positive = data.session.subsessions[i].positive
        data.subsession.negative = data.session.subsessions[i].negative
        data.subsession.score = data.session.subsessions[i].score
        document.getElementById('subsessionScore').innerHTML = (data.subsession.score) ? data.subsession.score : ''
        data.subsession.index = i
        $('#editSubsessionFeedback').modal({backdrop: "static"});
      },
      escapeAmpersand: function(str) {
        return str.replace(/&/g, "%26")
      },
      giveFeedback: function(){
        console.log("Start giveFeedback... mod")
        $( "#giveFeedbackForm" ).addClass("was-validated")
        if (data.session.feedback.positive == '' || data.session.feedback.negative == '' || data.session.feedback.score == null) return
        $( "#submitGiveFeedback" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitGiveFeedback" ).prop( "disabled", true );
        console.log("Escaped Ampersand:", this.escapeAmpersand(JSON.stringify(data.session)))
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              app.hideComponent('giveFeedback')
              app.showComponent('completedFeedback')
            } catch (e) {
              Swal.fire({
                icon: 'error',
                text: this.responseText
              })
              $( "#submitGiveFeedback" ).html("Retry give feedback?")
              $( "#submitGiveFeedback" ).prop( "disabled", false );
            }
          }
        };
        xhttp.open("POST", "api-v3/giveFeedback.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("session="+this.escapeAmpersand(JSON.stringify(data.session)));
      },
      giveFeedbackSeries: function(){
          console.log("Start giveFeedbackSeries...")
          console.log(data.session)
          $( "#giveFeedbackSeriesForm" ).addClass("was-validated")
          if (data.session.feedback.positive == '' || data.session.feedback.negative == '' || data.session.feedback.score == null) return
          for (let sub of data.session.subsessions) {
            if (sub.state == 'To do') {
              Swal.fire({
                icon: 'error',
                text: 'Please provide feedback for each session before continuing.'
              })
              return
            }
          }

          $( "#submitGiveFeedbackSeries" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
          $( "#submitGiveFeedbackSeries" ).prop( "disabled", true );
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              try {
                var res = JSON.parse(this.responseText)
                app.hideComponent('giveFeedbackSeries')
                app.showComponent('completedFeedback')
              } catch (e) {
                Swal.fire({
                  icon: 'error',
                  html: this.responseText
                })
                $( "#submitGiveFeedbackSeries" ).html("Retry give feedback?")
                $( "#submitGiveFeedbackSeries" ).prop( "disabled", false );
              }
            }
          };
          xhttp.open("POST", "api-v3/giveFeedbackSeries.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.send("session="+this.escapeAmpersand(JSON.stringify(data.session)));

      },
      giveFeedbackSubsession: function(){
        console.log("Start giveFeedbackSubsession...")
        $( "#giveFeedbackSubsessionForm" ).addClass("was-validated")
        
        if (data.subsession.positive == null || data.subsession.positive == '' || data.subsession.negative == null || data.subsession.negative == '' || data.subsession.score == null) return

        let i = data.subsession.index
        data.session.subsessions[i].state = "Complete"
        data.session.subsessions[i].name = "Complete"
        data.session.subsessions[i].name = data.subsession.name
        data.session.subsessions[i].title = data.subsession.title
        data.session.subsessions[i].positive = data.subsession.positive
        data.session.subsessions[i].negative = data.subsession.negative
        data.session.subsessions[i].score = data.subsession.score
        
        $( "#giveFeedbackSubsessionForm" ).removeClass("was-validated")
        $('#editSubsessionFeedback').modal('hide');
      },
      skipFeedbackSubsession: function(){
        console.log("Start skipFeedbackSubsession...")
        let i = data.subsession.index
        data.session.subsessions[i].state = "Skipped"
        data.session.subsessions[i].name = "Skipped"
        data.session.subsessions[i].name = data.subsession.name
        $('#editSubsessionFeedback').modal('hide');
      },
      showComponent: function(comp){
        console.log('showComponent: ' + comp)
        data.show[comp] = true
      },
      hideComponent: function(comp){
        console.log('hideComponent: ' + comp)
        data.show[comp] = false
      },
      copy: function(copyItem){
        console.log('Start copy('+copyItem+')')
        // Query the elements
        const codeEle = document.getElementById(copyItem);
        const selection = window.getSelection();

        // Save the current selection
        const currentRange = selection.rangeCount === 0
            ? null : selection.getRangeAt(0);

        // Select the text content of code element
        const range = document.createRange();
        range.selectNodeContents(codeEle);
        selection.removeAllRanges();
        selection.addRange(range);

        // Copy to the clipboard
        try {
            document.execCommand('copy');
            if (this.isIE()){
              this.showAlert('Copy button may not work correctly depending on your browser security settings. Use Ctrl+C instead.')
            } else {
              this.showNotice('Copied to clipboard.')
            }
        } catch (err) {
            // Unable to copy
            this.showAlert('Error copying to clipboard.')
        } finally {
            // Restore the previous selection
            selection.removeAllRanges();
            currentRange && selection.addRange(currentRange);
        }
      },
      isIE: function() {
        const ua = window.navigator.userAgent; //Check the userAgent property of the window.navigator object
        const msie = ua.indexOf('MSIE '); // IE 10 or older
        const trident = ua.indexOf('Trident/'); //IE 11
      
        return (msie > 0 || trident > 0);
      },
      selectText: function(id){
        var sel, range;
        var el = document.getElementById(id); //get element id
        if (window.getSelection && document.createRange) { //Browser compatibility
          sel = window.getSelection();
          if(sel.toString() == ''){ //no text selection
           window.setTimeout(function(){
            range = document.createRange(); //range object
            range.selectNodeContents(el); //sets Range
            sel.removeAllRanges(); //remove all ranges from selection
            sel.addRange(range);//add Range to a Selection.
          },1);
          }
        }else if (document.selection) { //older ie
          sel = document.selection.createRange();
          if(sel.text == ''){ //no text selection
            range = document.body.createTextRange();//Creates TextRange object
            range.moveToElementText(el);//sets Range
            range.select(); //make selection.
          }
        }
      },
      logger: function(msg){
          //for debugging on mobiles
         var log = $( "#logger" ).html()
         $( "#logger" ).html(log + "<br>" + msg)
      },
      showQuote: function(){
        const random = Math.floor(Math.random() * data.quoteList.length);
        data.quote = "<q>" + data.quoteList[random].quote + "</q><br><i>– " + data.quoteList[random].cite + "</i><br><a onclick='app.showQuote()'><i id='reload' class='fas fa-redo'></i></a>"
      },
      toggleCertificate: function(){
        data.session.certificate = !data.session.certificate
        console.log('session.certficiate: ' + data.session.certificate)
        if (data.session.certificate) {
          $( "#toggleCertificate" ).html("Disable certificate")
        } else {
          $( "#toggleCertificate" ).html("Enable certificate")
        }
        
      },
      toggleNotifications: function(){
        data.session.notifications = !data.session.notifications
        console.log('session.notifications: ' + data.session.notifications)
        if (data.session.notifications) {
          $( "#toggleNotifications" ).html("Disable notifications")
        } else {
          $( "#toggleNotifications" ).html("Enable notifications")
        }
      },
      downloadCertificate: function(){
        $( "#getCertificateForm" ).addClass("was-validated")
        if (data.certName == '' || data.certOrg == '') return
        document.getElementById("getCertificateForm").submit();
        $( "#certOrgSelect" ).prop('disabled',true)
        $( "#certOrg" ).prop('readonly',true)
        $( "#certName" ).prop('readonly',true)
      },
      scoreChange: function(subsession){
        let x = (subsession) ? data.subsession.score : data.session.feedback.score        
        var y = 'slider error'
        if (x>95){
          y = 'an overwhelmingly excellent session, couldn\'t be improved'
        } else if (x>80) {
          y = 'an excellent sesssion, minimal grounds for improvement'
        } else if (x>70) {
          y = 'a very good session, minor points for improvement'
        } else if (x>60) {
          y = 'a fairly good session, could be improved further'
        } else if (x>40) {
          y = 'basically sound, but needs further development'
        } else if (x>=20) {
          y = 'not adequate in its current state'
        } else if (x < 20) {
          y = 'an extremely poor session'
        }

        if (subsession) {
          data.subsession.scoreText = y
          document.getElementById('subsessionScoreText').value = y
          document.getElementById('subsessionScore').innerHTML = x
        } else {
          data.session.feedback.scoreText = y
        }
        
      },
      resetPIN: function(){
        console.log('Start resetPIN...')
        Swal.fire({
          title: 'Confirm PIN reset?',
          text: 'A new PIN will be sent to your email address.',
          showCancelButton: true
        }).then((result) => {
          if (result.isConfirmed) {
            var xhttp = new XMLHttpRequest()
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                console.log (this.responseText)
                try {
                  var res = JSON.parse(this.responseText)
                  Swal.fire({
                    icon: 'success',
                    text: res.msg
                  })
                } catch (e) {
                  console.log(e)
                  Swal.fire({
                    icon: 'error',
                    text: this.responseText
                  })
                }
              }
            };
            xhttp.open("POST", "api-v3/resetPIN.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id="+(data.session.id))
          }
        })
      },
      notificationPreferences: function(enable){
        console.log("Start notificationPreferences...", enable)
        if (enable) {
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              console.log (this.responseText)
              try {
                var res = JSON.parse(this.responseText)
                Swal.fire({
                  icon: 'success',
                  text: res.msg
                })
                data.session.id = ''
                app.hideComponent('loader')
                app.showComponent('welcome')
              } catch (e) {
                console.log(e)
                Swal.fire({
                  icon: 'error',
                  text: this.responseText
                })
              }
            }
          };
          xhttp.open("POST", "api-v3/notifications.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.send("notifications=true&id="+data.session.id)
        } else {
          Swal.fire({
            title: 'Confirm disable notifications?',
            text: 'You will no longer receive an email when an attendee submits feedback for your session.',
            showCancelButton: true
          }).then((result) => {
            if (result.isConfirmed) {
              var xhttp = new XMLHttpRequest()
              xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                  console.log (this.responseText)
                  try {
                    var res = JSON.parse(this.responseText)
                    Swal.fire({
                      icon: 'success',
                      text: res.msg
                    })
                    data.session.id = ''
                    app.hideComponent('loader')
                    app.showComponent('welcome')
                  } catch (e) {
                    console.log(e)
                    Swal.fire({
                      icon: 'error',
                      text: this.responseText
                    })
                  }
                }
              };
              xhttp.open("POST", "api-v3/notifications.php", true);
              xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhttp.send("notifications=false&id="+data.session.id)
            } else {
                data.session.id = ''
                app.hideComponent('loader')
                app.showComponent('welcome')
            }
          })
        }
      },
      selectOrg: function(){
        console.log("Start selectOrg...")
        if (data.certOrg == "Other...") {
          $( "#certOrgSelect" ).prop('hidden',true)
          $( "#certOrg" ).prop('hidden',false)
          data.certOrg = ''
        }
      }
    },
    mounted: function() {
      let args = "?DAqc2l"
      if (args.includes("?view=")) {
        data.session.id = args.replace("?view=","")
        console.log("Autodirect view feedback for session: " + data.session.id)
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
        this.hideComponent("loader")
        this.showComponent("enterPin")
      } else if (args.includes("?notifications=")) {
        let argsArray = args.split("&")
        console.log(argsArray)
        data.session.id = argsArray[1].replace("id=","")
        let enable = (argsArray[0].replace("?notifications=","") == 'true') ? true : false
        console.log("Autodirect disable notifications for session: " + data.session.id)
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
        this.notificationPreferences(enable)
      } else {
        data.session.id = args.replace("?","")
        if (data.session.id){
          console.log("Autodirect give feedback for session: " + data.session.id)
          window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
          this.getSession()
        } else {
          this.hideComponent('loader')
          this.showComponent('welcome')
        }
      }
      this.showQuote()
    }
  })
