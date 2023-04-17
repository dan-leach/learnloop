console.log ("app-v3.js running...")


var app = new Vue({
    el: '#app',
    data() {
      return {
        cookies: [],
        viewID: '',
        viewPDFid: '',
        certID: '',
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
          questions: [],
          hasQuestions: false,
          notifications: true,
          attendance: true,
          tags: false,
          pin: '',
          feedback: {
            positive: [],
            negative: [],
            questions: [],
            tags: {
              positive: [],
              negative: []
            },
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
            tags: [],
            score: null,
            scoreText: '',
            index: 0,
            state: "To do"
          }
        },
        question: {
          title: '',
          type: '',
          options: [],
          response: ''
        },
        feedback: {
          positiveComments: '',
          constructiveComments: '',
          questions: [],
          score: null,
          scoreText: 'Please indicate an overall score using the slider.'
        },
        tags: {
          positive: [
            'Kept to time well',
            'Answered questions from the audience effectively',
            'Good use of interactivity',
            'Slides effectively supported presentation'
          ],
          negative: [
            'Too much text on slides',
            'Just reading off the slides',
            'Would benefit from more interactivity',
            'Technical difficulties'
          ]
        },
        show: {
          loader: true,
          welcome: false,
          enterPin: false,
          viewFeedback: false,
          attendanceReport: false,
          giveFeedback: false,
          giveFeedbackSeries: false,
          completedFeedback: false,
          certificate: false,
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
    },
    methods: {
      getSession: function(){
        console.log("Start getSession...")
        if (this.session.id == '') return
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?"+this.session.id)
        for (let cookie of this.cookies) if (cookie.id == this.session.id) {
            Swal.fire({
              title: 'Resume feedback session?',
              icon: 'info',
              iconColor: '#17a2b8',
              text: 'You previously started filling in this feedback form. Would you like to pick up where you left off?',
              confirmButtonText: "Yes, let's go",
              confirmButtonColor: '#17a2b8',
              showDenyButton: true,
              denyButtonText: 'No, and clear my previous entry',
              allowOutsideClick: false,
              allowEscapeKey: false
            }).then((result) => {
              if (result.isConfirmed) {
                console.log("Load feedback data from cookie with ID: " + cookie.id)
                app.session = cookie
                app.hideComponent('loader')
                app.hideComponent('welcome')
                if (app.session.subsessions) {
                    app.showComponent('giveFeedbackSeries')
                } else {
                    app.showComponent('giveFeedback')
                }
                return
              }
            })
        }
        $( "#getExistingSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#getExistingSession" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              if (res.redirect == "v2") {
                console.log("Redirecting to v2")
                window.location.replace("https://learnloop.co.uk/v2.php?"+this.session.id);
                return
              }
              console.log('Session data retrieved: ', res)
              app.session.title = res.title
              app.session.date = res.date
              app.session.name = res.name
              app.session.subsessions = res.subsessions
              if (res.questions) app.session.questions = JSON.parse(res.questions)
              app.session.certificate = (res.certificate == true) ? true : false;
              app.session.attendance = (res.attendance == true) ? true : false;
              app.session.tags = (res.tags == true) ? true : false;
              app.session.tags = false //tags disabled for now
              if (app.session.subsessions) {
                for (let sub of app.session.subsessions) sub.state = "To do"
                app.hideComponent('loader')
                app.hideComponent('welcome')
                app.showComponent('giveFeedbackSeries')
              } else {
                app.hideComponent('loader')
                app.hideComponent('welcome')
                app.showComponent('giveFeedback')
              }
            } catch (e) {
              console.log(e)
              app.session.id = ''
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
        xhttp.open("GET", "api-v3/getSession.php?id="+this.session.id, true)
        xhttp.send()
      },
      enterPIN: function(){
        console.log("Start enterPIN...")
        if (this.viewID == '') return
        this.session.id = this.viewID
        this.hideComponent('welcome')
        this.showComponent('enterPin')
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?view="+this.session.id)
      },
      viewFeedback: function(){
        console.log("Start viewFeedback...")
        this.session.pin = this.session.pin.trimEnd()
        $( "#submitPin" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitPin" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              try {
                var res = JSON.parse(this.responseText)
                console.log(res)
                app.session.name = res.name
                app.session.title = res.title
                app.session.date = res.date
                app.session.questions = res.questions
                app.session.feedback.positive = (res.positive)
                app.session.feedback.negative = (res.negative)
                let sum = 0
                for (let score of res.score) sum += parseInt(score)
                app.session.feedback.score = Math.round((sum/res.score.length)*10)/10
                if (Number.isNaN(app.session.feedback.score)) app.session.feedback.score = "-"
                app.session.subsessions = res.subsessions
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
          xhttp.send("id="+this.session.id+"&pin="+this.session.pin);
      },
      createSession: function(){
        console.log("Start createSession...")
        $( "#createSessionForm" ).addClass("was-validated")
        if (this.session.title == '' || this.session.date == '' || this.session.name == '' || this.session.email == '') return
        $( "#submitCreateSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSession" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              app.session.id = res.id
              app.session.pin = res.pin
              app.session.date = res.date
              var tempLink = 'https://learnloop.co.uk/?' + app.session.id
              app.invite.full = "Dear attendee,<br><br>You attended my session <strong>'" + app.session.title + "'</strong> on " + app.session.date + ". I would be grateful if you would take the time to provide some anonymous feedback for me using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://learnloop.co.uk'>learnloop.co.uk</a></strong> and enter the session ID code: <strong>" + app.session.id + "</strong><br><br>Many thanks,<br>" + app.session.name + "<br><br><i>You can request feedback for your own sessions too using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>"
              app.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
              app.invite.view = 'https://learnloop.co.uk/?view=' + app.session.id
              app.invite.qr = 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8'
              app.hideComponent('createSession')
              app.showComponent('requestFeedback')
              window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?sessionCreated")
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
        xhttp.send("session="+JSON.stringify(this.session));
      },
      createSessionSeries: function(){
        console.log("Start createSessionSeries...")
        $( "#createSessionSeriesForm" ).addClass("was-validated") // add validation formatting to form
        if (this.session.title == '' || this.session.date == '' || this.session.name == '' || this.session.email == '') return //exit if any fields blank
        if (this.session.subsessions.length == 0) { //alert and exit if no subsessions
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
            try {
              var res = JSON.parse(this.responseText)
              app.session.id = res.id
              app.session.pin = res.pin
              app.session.date = res.date
              var tempLink = 'https://learnloop.co.uk/?' + app.session.id
              app.invite.full = "Dear attendee,<br><br>You attended the session series <strong>'" + app.session.title + "'</strong> on " + app.session.date + ". I would be grateful if you would take the time to provide some anonymous feedback for the sessions using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://learnloop.co.uk'>learnloop.co.uk</a></strong> and enter the session ID code: <strong>" + app.session.id + "</strong><br><br>Many thanks,<br>" + app.session.name + "<br><br><i>You can request feedback for your own sessions too using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>"
              app.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
              app.invite.view = 'https://learnloop.co.uk/?view=' + app.session.id
              app.invite.qr = 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8'
              app.hideComponent('createSessionSeries')
              app.showComponent('requestFeedback')
              window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?sessionSeriesCreated")
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
        xhttp.send("session="+JSON.stringify(this.session))
      },
      createQuestion: function(){
        console.log('Start createQuestion...')
        $( "#createQuestionForm" ).addClass("was-validated") //add validation formatting to form
        if (this.question.title == '' || this.question.type == '') return //exit validation if any fields blank
        if (this.question.type == 'checkbox' || this.question.type == 'select'){
          if (this.question.options == '') return
          let optionTitleArray = this.question.options.split(";")
          console.log(optionTitleArray)
          let options = []
          for (let o in optionTitleArray){
            console.log(o, optionTitleArray[o])
            let title = optionTitleArray[o]
            if (title.length > 0)options.push({title: title,selected: false})
          }
          console.log(options)
          this.question.options = options
        }
        $( "#submitCreateQuestion" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateQuestion" ).prop( "disabled", true );
        this.session.questions.push({...this.question})
        //reset form ready for next question add
        this.question.title = ''
        this.question.type = ''
        this.question.options = ''
        $( "#createQuestionForm" ).removeClass("was-validated")
        $("#submitCreateQuestion" ).html("Add to series")
        $("#submitCreateQuestion" ).prop("disabled", false )
        $('#addQuestion').modal('hide');
      },
      removeQuestion: function(i){
        console.log("Start removeQuestion...")
        Swal.fire({
          title: 'Remove this question?',
          showCancelButton: true,
          confirmButtonText: 'Yes',
        }).then((result) => {
          if (result.isConfirmed) {
            app.session.questions.splice(i,1)
          }
        })
      },
      createSubsession: function(){
        $( "#createSubsessionForm" ).addClass("was-validated") //add validation formatting to form
        if (this.subsession.title == '' || this.subsession.name == '') return //exit validation if any fields blank
        if (this.subsession.email == '') {
            Swal.fire({
                title: "Are you sure you don't want to provide a facilitator email?",
                text: "If you don't provide an email for the facilitator of this session they won't be able to view their feedback directly. As the organiser, you will still be able to view feedback for their session and share it with them manually if you wish. Click 'Cancel' if you want to return and enter a faciltator email.",
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    app.createSubsessionPart2()
                }
            })
        } else {
            this.createSubsessionPart2()
        }
      },
      createSubsessionPart2: function(){
        $( "#submitCreateSubsession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSubsession" ).prop( "disabled", true );
        if (this.subsession.email) {
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
    
                  app.session.subsessions.push({...app.subsession})
                  
                  //reset form ready for next subsession add
                  app.subsession.title = ''
                  app.subsession.name = ''
                  app.subsession.email = ''
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
            xhttp.send("email="+this.subsession.email);
        } else {
            this.session.subsessions.push({...this.subsession})
                  
          //reset form ready for next subsession add
          this.subsession.title = ''
          this.subsession.name = ''
          this.subsession.email = ''
          $( "#createSubsessionForm" ).removeClass("was-validated")
          $("#submitCreateSubsession" ).html("Add to series")
          $("#submitCreateSubsession" ).prop("disabled", false )
          $('#addSubsession').modal('hide');
        }
      },
      removeSubsession: function(i){
        console.log("Start removeSubsession...")
        Swal.fire({
          title: 'Remove this session?',
          showCancelButton: true,
          confirmButtonText: 'Yes',
        }).then((result) => {
          if (result.isConfirmed) {
            app.session.subsessions.splice(i,1)
          }
        })
      },
      editSubsessionFeedback: function(i){
        console.log("Start editSubsessionFeedback...")
        
        this.subsession.name = this.session.subsessions[i].name
        this.subsession.title = this.session.subsessions[i].title
        this.subsession.positive = this.session.subsessions[i].positive
        this.subsession.negative = this.session.subsessions[i].negative
        this.subsession.score = this.session.subsessions[i].score
        document.getElementById('subsessionScore').innerHTML = (this.subsession.score) ? this.subsession.score : ''
        this.subsession.index = i
        $('#editSubsessionFeedback').modal({backdrop: "static"});
      },
      escapeAmpersand: function(str) {
        return str.replace(/&/g, "%26")
      },
      giveFeedback: function(){
        console.log("Start giveFeedback...")
        $( "#giveFeedbackForm" ).addClass("was-validated")
        let pass = true
        if (this.session.feedback.positive == '' || this.session.feedback.negative == '' || this.session.feedback.score == null) pass = false
        for (let q in this.session.questions) {
          let question = this.session.questions[q]
          if (question.type == "checkbox") {
            question.response = ''
            for (let o in question.options) {
              let option = question.options[o]
              if (option.selected) question.response += option.title + ";"
            }
          } else {
            if (question.response == null || question.response === 'undefined') pass = false
          }
        }
        if (!pass) return
        $( "#submitGiveFeedback" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitGiveFeedback" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            try {
              var res = JSON.parse(this.responseText)
              app.hideComponent('giveFeedback')
              app.showComponent('completedFeedback')
              if (app.session.certificate) app.showComponent('certificate')
              document.cookie = app.session.id+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //need to clear once submitted instead
              app.certID = app.session.id
              app.session = ''
              window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
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
        xhttp.send("session="+this.escapeAmpersand(JSON.stringify(this.session)));
      },
      giveFeedbackSeries: function(){
          console.log("Start giveFeedbackSeries...")
          $( "#giveFeedbackSeriesForm" ).addClass("was-validated")
          let pass = true
          if (this.session.feedback.positive == '' || this.session.feedback.negative == '' || this.session.feedback.score == null) pass = false
          for (let q in this.session.questions) {
            let question = this.session.questions[q]
            if (question.type == "checkbox") {
              question.response = ''
              for (let o in question.options) {
                let option = question.options[o]
                if (option.selected) question.response += option.title + ";"
              }
            } else {
              if (question.response == null || question.response === 'undefined') pass = false
            }
          }          
          for (let sub of this.session.subsessions) {
            if (sub.state == 'To do') {
              Swal.fire({
                icon: 'error',
                text: 'Please provide feedback for each session before continuing.'
              })
              pass = false
              return
            }
          }

          if (!pass) return

          $( "#submitGiveFeedbackSeries" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
          $( "#submitGiveFeedbackSeries" ).prop( "disabled", true );
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              try {
                var res = JSON.parse(this.responseText)
                app.hideComponent('giveFeedbackSeries')
                app.showComponent('completedFeedback')
                if (app.session.certificate) app.showComponent('certificate')
                document.cookie = app.session.id+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //need to clear once submitted instead
                app.certID = app.session.id
                app.session = ''
                window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
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
          xhttp.send("session="+this.escapeAmpersand(JSON.stringify(this.session)));

      },
      giveFeedbackSubsession: function(){
        console.log("Start giveFeedbackSubsession...")
        $( "#giveFeedbackSubsessionForm" ).addClass("was-validated")
        
        if (this.subsession.positive == null || this.subsession.positive == '' || this.subsession.negative == null || this.subsession.negative == '' || this.subsession.score == null) return

        let i = this.subsession.index
        this.session.subsessions[i].state = "Complete"
        this.session.subsessions[i].name = "Complete"
        this.session.subsessions[i].name = this.subsession.name
        this.session.subsessions[i].title = this.subsession.title
        this.session.subsessions[i].positive = this.subsession.positive
        this.session.subsessions[i].negative = this.subsession.negative
        this.session.subsessions[i].score = this.subsession.score
        
        $( "#giveFeedbackSubsessionForm" ).removeClass("was-validated")
        $('#editSubsessionFeedback').modal('hide');
      },
      skipFeedbackSubsession: function(){
        console.log("Start skipFeedbackSubsession...")
        Swal.fire({
            title: 'Skip providing feedback for this session?',
            text: "Any feedback you've already for this session will be cleared.",
            showCancelButton: true
          }).then((result) => {
            if (result.isConfirmed) {
                let i = app.subsession.index
                app.session.subsessions[i].state = "Skipped"
                app.subsession.index = -1
                $('#editSubsessionFeedback').modal('hide'); 
            }
          })
      },
      showComponent: function(comp){
        console.log('Showing: [' + comp + ']')
        this.show[comp] = true
        window.scrollTo({top:0, left:0, behavior: 'smooth'})
        if (comp == "createSession") window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?createSession")
        if (comp == "createSessionSeries") window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?createSessionSeries")
      },
      hideComponent: function(comp){
        if (comp == "all") { for (let c in this.show) this.hideComponent(c) }
        else { this.show[comp] = false }
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
        const random = Math.floor(Math.random() * this.quoteList.length);
        this.quote = "<q>" + this.quoteList[random].quote + "</q><br><i>– " + this.quoteList[random].cite + "</i><br><a onclick='app.showQuote()'><i id='reload' class='fas fa-redo'></i></a>"
      },
      toggleCertificate: function(){
        this.session.certificate = !this.session.certificate
        console.log('session.certficiate: ' + this.session.certificate)
        if (this.session.certificate) {
          $( "#toggleCertificate" ).html("Disable certificate")
          $( "#toggleAttendance" ).html("Disable register of attendance")
          this.session.attendance = true
        } else {
          $( "#toggleCertificate" ).html("Enable certificate")
          $( "#toggleAttendance" ).html("Enable register of attendance")          
          this.session.attendance = false
        }
        
      },
      toggleNotifications: function(){
        this.session.notifications = !this.session.notifications
        console.log('session.notifications: ' + this.session.notifications)
        if (this.session.notifications) {
          $( "#toggleNotifications" ).html("Disable notifications")
        } else {
          $( "#toggleNotifications" ).html("Enable notifications")
        }
      },
      toggleTags: function(){
        this.session.tags = !this.session.tags
        console.log('session.tags: ' + this.session.tags)
        if (this.session.tags) {
          $( "#toggleTags" ).html("Disable feedback quick tags")
        } else {
          $( "#toggleTags" ).html("Enable feedback quick tags")
        }
      },
      toggleAttendance: function(){
        if (!this.session.certificate) {
          Swal.fire({
            icon: 'error',
            text: `You must enable the 'Certificate of Attendance' option to be able to use the 'Register of Attendance' option.`
          })
          return
        }
        this.session.attendance = !this.session.attendance
        console.log('session.attendance: ' + this.session.attendance)
        if (this.session.attendance) {
          $( "#toggleAttendance" ).html("Disable register of attendance")
        } else {
          $( "#toggleAttendance" ).html("Enable register of attendance")
        }
      },
      downloadCertificate: function(){
        $( "#getCertificateForm" ).addClass("was-validated")
        if (this.certName == '' || this.certOrg == '') return
        document.getElementById("getCertificateForm").submit();
        $( "#certOrgSelect" ).prop('disabled',true)
        $( "#certOrg" ).prop('readonly',true)
        $( "#certName" ).prop('readonly',true)
      },
      scoreChange: function(subsession){
        let x = (subsession) ? this.subsession.score : this.session.feedback.score        
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
          this.subsession.scoreText = y
          document.getElementById('subsessionScoreText').value = y
          document.getElementById('subsessionScore').innerHTML = x
        } else {
          this.session.feedback.scoreText = y
        }
        
      },
      resetPIN: function(){
        console.log('Start resetPIN...')
        if (!this.session.id){
          Swal.fire({
            icon: 'error',
            text: 'Please enter the session ID you want to reset the PIN for.'
          })
          return
        }
        Swal.fire({
          title: 'Confirm PIN reset?',
          text: 'A new PIN will be sent to your email address.',
          showCancelButton: true
        }).then((result) => {
          if (result.isConfirmed) {
            var xhttp = new XMLHttpRequest()
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
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
            xhttp.send("id="+(app.session.id))
          }
        })
      },
      notificationPreferences: function(enable){
        console.log("Start notificationPreferences...", enable)
        if (enable) {
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              try {
                var res = JSON.parse(this.responseText)
                Swal.fire({
                  icon: 'success',
                  text: res.msg
                })
                app.session.id = ''
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
          xhttp.send("notifications=true&id="+this.session.id)
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
                  try {
                    var res = JSON.parse(this.responseText)
                    Swal.fire({
                      icon: 'success',
                      text: res.msg
                    })
                    app.session.id = ''
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
              xhttp.send("notifications=false&id="+app.session.id)
            } else {
                app.session.id = ''
                app.hideComponent('loader')
                app.showComponent('welcome')
            }
          })
        }
      },
      selectOrg: function(){
        console.log("Start selectOrg...")
        if (this.certOrg == "Other...") {
          $( "#certOrgSelect" ).prop('hidden',true)
          $( "#certOrg" ).prop('hidden',false)
          this.certOrg = ''
        }
      },
      saveProgress: function(confirm){
        if (this.show.giveFeedback || this.show.giveFeedbackSeries) {
            const d = new Date()
            d.setTime(d.getTime() + (1*24*60*60*1000)) //1 day
            let expires = "expires="+ d.toUTCString()
            if (this.session.id) document.cookie = this.session.id+"="+JSON.stringify(this.session) + ";" + expires +  ";path=/;" //stores as cookie with name of session ID
            console.log("Save progress", document.cookie)
            if (confirm) Swal.fire({
              icon: 'success',
              title: 'Your progress has been saved',
              text: 'Return to this form on the same device within the next 24 hours to pick up where you left off.'
            })
        }
      }
    },
    mounted: function() {
      if (document.cookie) {
          try{    
            let raw = document.cookie
            let split = raw.split("; ")
            let sliced = []
            console.log(sliced)
            for (let sp of split) sliced.push(sp.slice(7))
            for (let sl of sliced) this.cookies.push(JSON.parse(sl))
          } catch (e) {
              console.log(e)
          }
      }
      let args = window.location.search
      if (args.includes("?view=")) {
        this.session.id = args.replace("?view=","")
        console.log("Autodirect view feedback for session: " + this.session.id)
        this.hideComponent("loader")
        this.showComponent("enterPin")
      } else if (args.includes("?resetPIN=")) {
        this.session.id = args.replace("?resetPIN=","")
        console.log("Autodirect reset PIN for session: " + this.session.id)
        this.hideComponent("loader")
        this.showComponent("enterPin")
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
        this.resetPIN()
      } else if (args.includes("?notifications=")) {
        let argsArray = args.split("&")
        this.session.id = argsArray[1].replace("id=","")
        let enable = (argsArray[0].replace("?notifications=","") == 'true') ? true : false
        console.log("Autodirect disable notifications for session: " + this.session.id)
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
        this.notificationPreferences(enable)
      } else if (args.includes("?attendance=")) {
          this.session.id = args.replace("?attendance=","")
          console.log("Autodirect view attendance for session: " + this.session.id)
          this.hideComponent("loader")
          this.showComponent("attendanceReport")
      } else if (args.includes("?createSessionSeries")) {
          this.hideComponent("loader")
          this.showComponent("createSessionSeries")
      } else if (args.includes("?createSession")) {
          this.hideComponent("loader")
          this.showComponent("createSession")
      } else if (args.includes("Created")) {
          window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/") //don't reload the completed create session page  
          this.hideComponent("loader")
          this.showComponent('welcome')
      } else {
        this.session.id = args.replace("?","")
        if (this.session.id){
          console.log("Autodirect give feedback for session: " + this.session.id)
          this.getSession()
        } else {
          this.hideComponent('loader')
          this.showComponent('welcome')
        }
      }
      this.showQuote()
    }
  })
  
window.onbeforeunload = function() { app.saveProgress()}