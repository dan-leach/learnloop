console.log ("app-v4.js running...")

const regionHospitals = [ //each array is of the hospitals in that region
    index0 = [], //to ensure that region input value correlates with index of appropriate array
    northernIreland = [
        "Altnagelvin Area Hospital, Londonderry",
        "Antrim Area Hospital, Antrim",
        "Causeway Hospital, Coleraine",
        "Craigavon Area Hospital, Craigavon",
        "Daisy Hill Hospital, Newry",
        "Royal Belfast Hospital for Sick Children, Belfast",
        "South West Acute Hospital, Enniskillen",
        "Ulster Hospital, Dundonald"
    ],
    scotland = [
        "Lothian",
		"Greater Glasgow",
		"Wishaw",
		"Borders",
		"Ayrshire & Arran",
		"Dumfries",
		"Highlands",
		"Grampian",
		"Tayside",
		"Fife",
		"Forth Valley"
    ],
    wales = [
        "Bronglais General Hospital",
        "Glan Clwyd Hospital",
        "Glangwili General Hospital",
        "Grange University Hospital",
        "Morriston Hospital",
        "Neath Port Talbot Hospital",
        "Prince Charles Hospital",
        "Princess of Wales Hospital",
        "Royal Glamorgan Hospital",
        "University Hospital of Wales",
        "Withybush General Hospital",
        "Wrexham Maelor Hospital",
        "Ysbyty Gwynedd"
    ],
    eastMidlands = [
        "Boston Pilgrim Hospital",
        "Chesterfield Royal",
        "Derby Hospitals",
        "Grantham and District Hospital",
        "Kettering General Hospital",
        "Leicester Royal Infirmary",
        "Lincoln County Hospital",
        "Northampton General Hospital",
        "Nottingham University Hospitals",
        "Sherwood Forest Hospitals"
    ],
    eastOfEngland = [
        "Addenbrookes Hospital", 
        "Basildon and Thurrock University Hospital", 
        "Bedford Hospital", 
        "Broomfield Hospital",
        "Colchester Hospital", 
        "East & North Herts", 
        "Hinchingbrooke Health Care", 
        "Ipswich Hospital", 
        "James Paget University Hospital", 
        "Luton & Dunstable University Hospital", 
        "Norfolk & Norwich University Hospital", 
        "Peterborough City Hospital", 
        "Princess Alexandra Hospital", 
        "Queen Elizabeth Hospital Kings Lynn", 
        "Queen Elizabeth II Hospital", 
        "Southend University Hospital", 
        "Watford General Hospital",
        "West Suffolk Hospital"
    ],
    northEastAndNorthCumbria = [
        "Bishop Aukland Hospital",
        "Darlington Memorial Hospital",
        "Friarage Hospital",
        "Great North Children's Hospital",
        "North Tyneside General Hospital",
        "Queen Elizabeth Hospital",
        "South Tyneside District Hospital",
        "Sunderland Royal Hospital",
        "The Cumberland Infirmary",
        "The James Cook University Hospital",
        "University Hospital of Hartlepool",
        "University Hospital of North Durham",
        "University Hospital of North Tees",
        "West Cumberland Hospital"
    ],
    northWest = [
        "Alder Hey Children’s",
        "Blackpool Teaching Hospitals",
        "Bolton",
        "Central Manchester University Hospitals",
        "Countess of Chester Hospital",
        "East Cheshire",
        "East Lancashire Hospitals",
        "Lancashire Teaching Hospitals",
        "Mid Cheshire Hospitals",
        "Salford Royal",
        "Southport and Ormskirk Hospital",
        "St Helens and Knowsley Teaching Hospitals",
        "Stockport",
        "Tameside & Glossop Integrated Care",
        "The Pennine Acute Hospitals",
        "University Hospital of South Manchester",
        "University Hospitals of Morecambe Bay",
        "Warrington and Halton Hospitals",
        "Wirral University Teaching Hospital",
        "Wrightington Wigan and Leigh"
    ],
    southEastCoastAndLondonPartnership = [
        "Barnet General Hospital",
        "Buckland Hospital",
        "Central Middlesex",
        "Chelsea and Westminster",
        "Conquest Hospital",
        "Croydon University Hospital",
        "Darent Valley Hospital",
        "Ealing Hospital",
        "East Surrey Hospital",
        "Eastbourne District General Hospital",
        "Epsom Hospital",
        "Evelina London Children’s Hospital",
        "Frimley Park Hospital",
        "Great Ormond Street Hospital for Children",
        "Hillingdon Hospitals",
        "Kent and Canterbury Hospital",
        "King George Hospital",
        "King's College Hospital",
        "Kingston Hospital",
        "Lewisham Hospital",
        "Maidstone Hospital",
        "Medway Maritime Hospital",
        "Newham University Hospital",
        "North Middlesex University Hospital",
        "Northwick Park",
        "Princess Royal University Hospital",
        "Queen Elizabeth Hospital",
        "Queen Elizabeth The Queen Mother Hospital",
        "Queen Mary's Hospital",
        "Queen's Hospital",
        "Royal Alexandra Children's Hospital",
        "Royal Free London",
        "Royal Surrey County Hospital",
        "Royal Victoria Hospital",
        "St George’s Hospital",
        "St Helier Hospital",
        "St. Mary’s Hospital",
        "St. Peter's Hospital",
        "St. Richards Hospital",
        "The Royal London Hospital",
        "The Whittington Hospital",
        "Tunbridge Wells Hospital",
        "University College Hospital",
        "West Middlesex University Hospital",
        "Whipps Cross University Hospital",
        "William Harvey Hospital",
        "Worthing Hospital"
    ],
    southWest = [
        "Gloucestershire Hospitals",
        "Great Western Hospitals",
        "Northern Devon Hospital",
        "Plymouth Hospitals",
        "Royal Cornwall Hospitals",
        "Royal Devon and Exeter Hospitals",
        "Royal United Hospitals Bath",
        "South Devon Healthcare",
        "Taunton and Somerset",
        "University Hospitals Bristol",
        "Yeovil District Hospital"  
    ],
    thamesValley = [
        "Frimley Health",
        "John Radcliffe Hospital",
        "Milton Keynes Hospital",
        "Royal Berkshire",
        "Stoke Mandeville Hospital",
        "Wycombe Hospital"
    ],
    wessex = [
        "Basingstoke and North Hampshire Hospital",
        "Dorset County Hospital",
        "Poole Hospital",
        "Queen Alexandra Hospital",
        "Royal Hampshire County Hospital",
        "Salisbury District Hospital",
        "Southampton General Hospital",
        "St. Mary's Hospital"
    ],
    westMidlands = [
        "Alexandra Hospital",
        "Birmingham Children’s Hospital",
        "Birmingham City Hospital",
        "Burton Hospitals",
        "County Hospital (Stafford)",
        "County Hospital (Wye Valley)",
        "George Eliot Hospital",
        "Good Hope Hospital",
        "Heartlands Hospital",
        "Hospital of St Cross",
        "Kidderminster Hospital",
        "New Cross Hospital",
        "Princess Royal Hospital",
        "Royal Shrewsbury Hospital",
        "Royal Stoke University Hospital",
        "Russells Hall Hospital",
        "Sandwell General Hospital",
        "Solihull Hospital",
        "South Warwickshire",
        "University Hospital Coventry",
        "Walsall Manor Hospital",
        "Worcestershire Royal Hospital"
    ],
    yorkshireAndHumber = [
        "Airedale General Hospital",
        "Barnsley Hospital",
        "Bassetlaw Hospital",
        "Bradford Royal Infirmary",
        "Calderdale Royal Hospital",
        "Dewsbury and District Hospital",
        "Diana, Princess of Wales Hospital",
        "Doncaster Royal Infirmary",
        "Harrogate District Hospital",
        "Huddersfield Royal Infirmary",
        "Hull Royal Infirmary",
        "Leeds Children's Hospital",
        "Pinderfields General Hospital",
        "Pontefract General Infirmary",
        "Rotherham Hospital",
        "Scarborough Hospital",
        "Scunthorpe General Hospital",
        "Sheffield Children's Hospital",
        "St.Luke's Hospital",
        "The York Hospital"
    ],
]

const quoteList = [
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

var app = new Vue({
    el: '#app',
    data() {
      return {
        cookies: [],
        cookieMsg: "If you can't complete your feedback in one sitting, click the 'Save progress' button below and return to this form on the same device within the next 24 hours to pick up where you left off.",
        certificate: {
          id: '',
          name: '',
          region: '',
          org: '',
          data: ''
        },
        session: {
          id: '',
          name: '',
          email: '',
          title: '',
          date: '',
          certificate: true,
          subsessions: [],
          isSubsession: false,
          questions: [],
          hasQuestions: false,
          notifications: true,
          attendance: true,
          attendees: [],
          attendeeCount: 0,
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
          index: -1,
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
        subsessionEmailPrompt: true,
        question: {
          title: '',
          type: '',
          options: [],
          response: '',
          index: -1
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
          welcome: false,
          loader: true,
          createSession: false,
          createSessionSeries: false,
          sessionCreated: false,
          updateDetails: false,
          updateDetailsSeries: false,
          giveFeedback: false,
          giveFeedbackSeries: false,
          completedFeedback: false,
          viewFeedback: false,
          viewAttendance: false
        },
        invite: {
          full: '',
          link: '',
          qr: ''
        },
        showCopyBtns: true,
        quote: ''
      }
    },
    methods: {
      //session creation
      enableQuestions: function(comp){
        console.log("start enableQuestions...")
        this.session.hasQuestions = true;
        
        this.hideComponent(comp)
        this.showComponent(comp)
      },
      createQuestion: function(){
        console.log('Start createQuestion...')
        $( "#createQuestionForm" ).addClass("was-validated") //add validation formatting to form
        if (this.question.title == '' || this.question.type == '') return //exit validation if any fields blank
        if (this.question.type == 'checkbox' || this.question.type == 'select'){
          if (this.question.options == '') return
          let optionTitleArray = this.question.options.split(";")
          let options = []
          for (let o in optionTitleArray){
            let title = optionTitleArray[o]
            if (title.length > 0)options.push({title: title,selected: false})
          }
          this.question.options = options
        }
        $( "#submitCreateQuestion" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateQuestion" ).prop( "disabled", true );
        if (this.question.index == -1){
          this.session.questions.push({...this.question})
        } else {
          this.session.questions[this.question.index].title = this.question.title
          this.session.questions[this.question.index].type = this.question.type
          this.session.questions[this.question.index].options = this.question.options
        }
        //reset form ready for next question add
        this.question.title = ''
        this.question.type = ''
        this.question.options = ''
        this.question.index = -1
        $( "#createQuestionForm" ).removeClass("was-validated")
        $("#submitCreateQuestion" ).html("Add question")
        $("#submitCreateQuestion" ).prop("disabled", false )
        $('#addQuestion').modal('hide');
      },
      editQuestion: function(i){
        console.log('Start editQuestion...')
        this.question.title = this.session.questions[i].title
        this.question.type = this.session.questions[i].type
        for (let o in this.session.questions[i].options) this.question.options += this.session.questions[i].options[o].title + ";"
        this.question.index = i
        $('#addQuestion').modal({backdrop: "static"})
        $( "#submitCreateQuestion" ).html("Update question")
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
      validateSubsession: function(){
        $( "#createSubsessionForm" ).addClass("was-validated") //add validation formatting to form
        if (this.subsession.title == '' || this.subsession.name == '') return //exit validation if any fields blank
        $( "#submitCreateSubsession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSubsession" ).prop( "disabled", true );
        if (this.subsession.email == '') {
          if (this.subsessionEmailPrompt) {
            this.subsessionEmailPrompt = false
            Swal.fire({
              title: "Are you sure you don't want to provide a facilitator email?",
              text: "If you don't provide an email for the facilitator of this session they won't be able to view their feedback directly. As the organiser, you will still be able to view feedback for their session and share it with them manually if you wish. Click 'Cancel' if you want to return and enter a faciltator email.",
              showCancelButton: true
            }).then((result) => {
              if (result.isConfirmed) {
                app.createSubsession()
              }
            })
            $("#submitCreateSubsession" ).html("Retry add to series?")
            $("#submitCreateSubsession" ).prop("disabled", false )
          } else {
            app.createSubsession()
          }
        } else {
          this.api("checkEmailIsValid", null, null, JSON.stringify(this.subsession.email)).then(
            function(){
              app.createSubsession()
            },
            function(error) {
              Swal.fire({
                icon: 'error',
                text: error
              })
              $("#submitCreateSubsession" ).html("Retry add to series?")
              $("#submitCreateSubsession" ).prop("disabled", false )
            }
          )
        }
      },
      createSubsession: function(){
        if (this.subsession.index == -1) {
          this.session.subsessions.push({...this.subsession})
        } else {
          this.session.subsessions[this.subsession.index].name = this.subsession.name
          this.session.subsessions[this.subsession.index].title = this.subsession.title
          this.session.subsessions[this.subsession.index].email = this.subsession.email
        }

        //reset form ready for next subsession add
        this.subsession.title = ''
        this.subsession.name = ''
        this.subsession.email = ''
        this.subsession.index = -1
        $( "#createSubsessionForm" ).removeClass("was-validated")
        $("#submitCreateSubsession" ).html("Add to series")
        $("#submitCreateSubsession" ).prop("disabled", false )
        $('#addSubsession').modal('hide');
      },
      editSubsession: function(i){
        console.log('Start editSubsession...')
        this.subsession.title = this.session.subsessions[i].title
        this.subsession.name = this.session.subsessions[i].name
        this.subsession.email = this.session.subsessions[i].email
        this.subsession.index = i
        $('#addSubsession').modal({backdrop: "static"})
        $( "#submitCreateSubsession" ).html("Update session")
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
      sortSubsession: function(i,x){
        this.session.subsessions.splice(i+x, 0, this.session.subsessions.splice(i, 1)[0])
      },
      createSession: function(){
        $( "#createSessionForm" ).addClass("was-validated")
        if (this.session.title == '' || this.session.date == '' || this.session.name == '' || this.session.email == '') return
        $( "#submitCreateSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreateSession" ).prop( "disabled", true );
        this.api('insertSession', null, null, this.escapeAmpersand(JSON.stringify(this.session))).then(
          function(res) {
            app.session.id = res.id
            app.session.pin = res.pin
            app.session.date = res.date
            let tempLink = 'https://learnloop.co.uk/?' + app.session.id
            app.invite.full = "Dear attendee,<br><br>You attended my session <strong>'" + app.session.title + "'</strong> on " + app.session.date + ". I would be grateful if you would take the time to provide some anonymous feedback for me using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://learnloop.co.uk'>learnloop.co.uk</a></strong> and enter the session ID code: <strong>" + app.session.id + "</strong><br><br>Many thanks,<br>" + app.session.name + "<br><br><i>You can request feedback for your own sessions using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>"
            app.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
            app.invite.view = 'https://learnloop.co.uk/?view=' + app.session.id
            app.invite.qr = 'https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=' + tempLink + '&choe=UTF-8&chld=h'
            app.hideComponent('createSession')
            app.showComponent('sessionCreated')
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?sessionCreated")
          },
          function(error) {
            $( "#submitCreateSession" ).html("Retry create session?")
            $( "#submitCreateSession" ).prop( "disabled", false )
            Swal.fire({
              icon: 'error',
              title: 'Unable to create session',
              text: error
            })
          }
        )
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
        this.api('insertSession', null, null, this.escapeAmpersand(JSON.stringify(this.session))).then(
          function(res) {
            app.session.id = res.id
            app.session.pin = res.pin
            app.session.date = res.date
            let tempLink = 'https://learnloop.co.uk/?' + app.session.id
            app.invite.full = "Dear attendee,<br><br>You attended the session series <strong>'" + app.session.title + "'</strong> on " + app.session.date + ". I would be grateful if you would take the time to provide some anonymous feedback for the sessions using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://learnloop.co.uk'>learnloop.co.uk</a></strong> and enter the session ID code: <strong>" + app.session.id + "</strong><br><br>Many thanks,<br>" + app.session.name + "<br><br><i>You can request feedback for your own sessions too using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>"
            app.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
            app.invite.view = 'https://learnloop.co.uk/?view=' + app.session.id
            app.invite.qr = 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8'
            app.hideComponent('createSessionSeries')
            app.showComponent('sessionCreated')
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?sessionCreated")
          },
          function(error) {
            $( "#submitCreateSessionSeries" ).html("Retry create session?")
            $( "#submitCreateSessionSeries" ).prop( "disabled", false )
            Swal.fire({
              icon: 'error',
              title: 'Unable to create session series',
              text: error
            })
          }
        )
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
      //session updates
      loadPinUpdateDetails: function(){
        console.log("Start loadPinUpdateDetails...")
        Swal.fire({
          title: 'Edit session details',
          html:
            'You will need your session ID and PIN which you can find in the email you received when your session was created. Please note that session details can only be edited prior to feedback being submitted.<br>'+
            '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
            '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          preConfirm: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.pin = document.getElementById('swalFormPin').value
          }
        }).then((result) => {
          if (result.isConfirmed) {
            if (!app.session.id || !app.session.pin) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and PIN.'
              })
              return
            }
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?edit="+this.session.id)
            this.loadUpdateDetails()
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      loadUpdateDetails: function(){
        console.log("Start loadUpdateDetails...")
        this.session.pin = this.session.pin.trimEnd()

        this.api("loadUpdateDetails", this.session.id, this.session.pin, null).then(
          function(res) {
            app.session.title = res.title
            app.session.date = res.date
            app.session.name = res.name
            app.session.email = res.email
            if (res.subsessions) app.session.subsessions = res.subsessions
            app.session.isSubsession = res.isSubsession
            if (res.questions.length >0) {
              app.session.questions = res.questions
              app.session.hasQuestions = true
            }
            app.session.certificate = (res.certificate == true) ? true : false;
            app.session.attendance = (res.attendance == true) ? true : false;
            app.session.tags = (res.tags == true) ? true : false;
            app.hideComponent('welcome')
            if (app.session.subsessions.length > 0) {
              app.showComponent('updateDetailsSeries')
            } else {
              app.showComponent('updateDetails')
            }
          },
          function(error) {
            app.hideComponent('loader')
            app.showComponent('welcome')
            Swal.fire({
              icon: 'error',
              title: 'Unable to load edit session form',
              text: error
            })
          }
        )
      },
      submitUpdateDetails: function(){
        console.log("Start submitUpdateDetails...")
        $( "#updateDetailsForm" ).addClass("was-validated")
        if (this.session.title == '' || this.session.date == '' || this.session.name == '' || this.session.email == '') return
        $( "#submitUpdateDetails" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitUpdateDetails" ).prop( "disabled", true );
        this.api('updateDetails', this.session.id, this.session.pin, this.escapeAmpersand(JSON.stringify(this.session))).then(
          function(res) {
            app.session.id = ''
            app.hideComponent('updateDetails')
            app.showComponent('welcome')
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
            Swal.fire({
              icon: 'success',
              text: res
            })
          },
          function(error) {
            $( "#submitUpdateDetails" ).html("Retry update session?")
            $( "#submitUpdateDetails" ).prop( "disabled", false )
            Swal.fire({
              icon: 'error',
              title: 'Unable to update session',
              text: error
            })
          }
        )
      },
      submitUpdateDetailsSeries: function(){
        console.log("Start submitUpdateDetailsSeries...")
        $( "#updateDetailsSeriesForm" ).addClass("was-validated")
        if (this.session.title == '' || this.session.date == '' || this.session.name == '' || this.session.email == '') return
        if (this.session.subsessions.length == 0) { //alert and exit if no subsessions
          Swal.fire({
            icon: 'error',
            text: 'You need to add at least one session to this session series.'
          })
          return
        }
        $( "#submitUpdateDetailsSeries" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitUpdateDetailsSeries" ).prop( "disabled", true );
        this.api('updateDetails', this.session.id, this.session.pin, this.escapeAmpersand(JSON.stringify(this.session))).then(
          function(res) {
            app.session.id = ''
            app.hideComponent('updateDetailsSeries')
            app.showComponent('welcome')
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
            Swal.fire({
              icon: 'success',
              text: res
            })
          },
          function(error) {
            $( "#submitUpdateDetailsSeries" ).html("Retry update session?")
            $( "#submitUpdateDetailsSeries" ).prop( "disabled", false )
            Swal.fire({
              icon: 'error',
              title: 'Unable to update session',
              text: error
            })
          }
        )
      },
      loadNotificationPreference: function(){
        console.log("Start loadNotificationPreference...")
        Swal.fire({
          title: 'Edit notification preferences details',
          html:
            'You will need your session ID and PIN which you can find in the email you received when your session was created.<br>'+
            '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
            '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
          confirmButtonText: 'Enable notifications',
          confirmButtonColor: '#007bff',
          showDenyButton: true,
          denyButtonText: 'Disable notifications',
          denyButtonColor: '#007bff',
          showCancelButton: true,
          preConfirm: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.pin = document.getElementById('swalFormPin').value
          },
          preDeny: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.pin = document.getElementById('swalFormPin').value
          }
        }).then((result) => {
          console.log(app.session.id, app.session.pin)
          if (result.isConfirmed) {
            if (!app.session.id || !app.session.pin) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and PIN.'
              })
              return
            }
            this.setNotificationPreference(true)
          } else if (result.isDenied) {
            if (!app.session.id || !app.session.pin) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and PIN.'
              })
              return
            }
            this.setNotificationPreference(false)
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      setNotificationPreference: function(value){
        console.log("Start setNotificationPreference...", value)
        this.api('setNotificationPreference', this.session.id, this.session.pin, value).then(
          function(res) {
            Swal.fire({
              icon: 'success',
              text: res
            })
          },
          function(error) {
            Swal.fire({
              icon: 'error',
              text: error
            })
          }
        )
      },
      resetPIN: function(){
        console.log("Start resetPIN...")
        Swal.fire({
          title: 'Reset PIN',
          html:
            'You will need your session ID which you can find in emails relating to your session, or in the link to your feedback form.<br>For example: learnloop.co.uk/?<mark>aBc123</mark>.<br>'+
            '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' + 
            '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          preConfirm: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.email = document.getElementById('swalFormEmail').value
          }
        }).then((result) => {
          if (result.isConfirmed) {
            if (!this.session.id || !this.session.email) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and facilitator email.'
              })
              return
            }
            this.api('resetPIN', this.session.id, null, JSON.stringify(this.session.email)).then(
              function(res) {
                Swal.fire({
                  icon: 'success',
                  text: res
                })
              },
              function(error) {
                Swal.fire({
                  icon: 'error',
                  text: error
                })
              }
            )
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      closeSession: function(){
        console.log("Start closeSession...")
        Swal.fire({
          title: 'Close session',
          html:
            'This will close your feedback request to further submissions. You will need your session ID and PIN which you can find in the email you received when your session was created.<br>'+
            '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
            '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          preConfirm: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.pin = document.getElementById('swalFormPin').value
          }
        }).then((result) => {
          if (result.isConfirmed) {
            if (!this.session.id || !this.session.pin) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and PIN.'
              })
              return
            }
            this.api('closeSession', this.session.id, this.session.pin, null).then(
              function(res) {
                Swal.fire({
                  icon: 'success',
                  text: res
                })
              },
              function(error) {
                Swal.fire({
                  icon: 'error',
                  text: error
                })
              }
            )
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      //give feedback
      loadGiveFeedback: function(){
        if (this.session.id == '') return
        $( "#getExistingSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#getExistingSession" ).prop( "disabled", true );
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
                if (!app.saveProgress(false)) app.cookieMsg = "LearnLoop isn't able to save your progress right now as cookies seem to be disabled. You won't be able to save your progress, but can still complete this form in one sitting."
                return
              }
            })
        }
        this.api("fetchDetails", this.session.id, null, null).then(
          function(res) {
            if (res.closed) {
              Swal.fire({
                icon: 'error',
                text: 'This feedback request has been closed by the facilitator.'
              })
              $( "#getExistingSession" ).html("Give feedback")
              $( "#getExistingSession" ).prop( "disabled", false );
              return
            }
            app.session.title = res.title
            app.session.date = res.date
            app.session.name = res.name
            app.session.subsessions = (res.subsessions.length > 0) ? res.subsessions : false
            if (res.questions) app.session.questions = JSON.parse(res.questions)
            app.session.certificate = (res.certificate == true) ? true : false;
            app.session.attendance = (res.attendance == true) ? true : false;
            app.session.tags = (res.tags == true) ? true : false;
            app.session.tags = false //tags disabled for now
            if (app.session.subsessions) {
              for (let x in app.session.subsessions) Vue.set(app.session.subsessions[x],'state','To do') //using Vue.set to enable reactivity for subsession state such that 'skipped' and 'completed' appear appropriately
              app.hideComponent('loader')
              app.hideComponent('welcome')
              app.showComponent('giveFeedbackSeries')
            } else {
              app.hideComponent('loader')
              app.hideComponent('welcome')
              app.showComponent('giveFeedback')
            }
            console.log(app.saveProgress(false))
            if (!app.saveProgress(false)) app.cookieMsg = "LearnLoop isn't able to save your progress right now as cookies seem to be disabled. You won't be able to save your progress, but can still complete this form in one sitting."
          },
          function(error) {
            app.hideComponent('loader')
            app.showComponent('welcome')
            $( "#getExistingSession" ).html("Give feedback")
            $( "#getExistingSession" ).prop( "disabled", false );
            Swal.fire({
              icon: 'error',
              title: 'Unable to load feedback form',
              text: error
            })
          }
        )
      },
      loadGiveSubsessionFeedback: function(i){
        console.log("Start editSubsessionFeedback...")
        
        this.subsession.name = this.session.subsessions[i].name
        this.subsession.title = this.session.subsessions[i].title
        this.subsession.positive = this.session.subsessions[i].positive
        this.subsession.negative = this.session.subsessions[i].negative
        this.subsession.score = this.session.subsessions[i].score
        document.getElementById('subsessionScore').innerHTML = (this.subsession.score) ? this.subsession.score : ''
        this.subsession.index = i
        $('#giveSubsessionFeedback').modal({backdrop: "static"});
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
      submitGiveSubsessionFeedback: function(){
        $( "#giveFeedbackSubsessionForm" ).addClass("was-validated")
        
        if (this.subsession.positive == null || this.subsession.positive == '' || this.subsession.negative == null || this.subsession.negative == '' || this.subsession.score == null) return

        let i = this.subsession.index
        this.session.subsessions[i].state = "Complete"
        this.session.subsessions[i].name = this.subsession.name
        this.session.subsessions[i].title = this.subsession.title
        this.session.subsessions[i].positive = this.subsession.positive
        this.session.subsessions[i].negative = this.subsession.negative
        this.session.subsessions[i].score = this.subsession.score
        
        $( "#giveFeedbackSubsessionForm" ).removeClass("was-validated")
        $( "#giveSubsessionFeedback").modal("hide");
        this.saveProgress(false);
      },
      skipSubsessionFeedback: function(){
        console.log("Start skipFeedbackSubsession...")
        let i = this.subsession.index
        app.session.subsessions[i].state = "Skipped"
        app.subsession.index = -1
        Swal.fire({
            title: 'Skip providing feedback for this session?',
            text: "Any feedback you've already for this session will be cleared.",
            showCancelButton: true
          }).then((result) => {
            if (result.isConfirmed) {
              $('#giveSubsessionFeedback').modal('hide'); 
            } else {
              app.session.subsessions[i].state = "To do"
              app.subsession.index = i
            }
          })
        this.saveProgress(false);  
      },
      saveProgress: function(confirm){
        if (this.show.giveFeedback || this.show.giveFeedbackSeries) {
            const d = new Date()
            d.setTime(d.getTime() + (1*24*60*60*1000)) //1 day
            let expires = "expires="+ d.toUTCString()
            if (this.session.id) document.cookie = this.session.id+"="+JSON.stringify(this.session) + ";" + expires +  ";path=/;" //stores as cookie with name of session ID
            console.log((document.cookie.includes(this.session.id)) ? "saveProgress success" : "saveProgress fail")
            if (confirm) {
              if (document.cookie.includes(this.session.id)) {
                  Swal.fire({
                      icon: 'success',
                      title: 'Your progress has been saved',
                      text: 'Return to this form on the same device within the next 24 hours to pick up where you left off.'
                  })
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Unable to save your progress',
                      text: "You can still submit your feedback, but you'll need to fill in the form in one sitting, rather than saving and returning to it later."
                  })
              }
            }
            return (document.cookie.includes(this.session.id)) ? true : false
        }
      },
      submitGiveFeedback: function(){
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
        this.api('insertFeedback', this.session.id, null, this.escapeAmpersand(JSON.stringify({'feedback': this.session.feedback, 'questions': this.session.questions}))).then(
          function(res) {
            app.hideComponent('giveFeedback')
            app.showComponent('completedFeedback')
            if (app.session.certificate) app.showComponent('certificate')
            document.cookie = app.session.id+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //need to clear once submitted instead
            app.certificate.id = app.session.id
            app.session = ''
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
          },
          function(error) {
            $( "#submitGiveFeedback" ).html("Retry give feedback?")
            $( "#submitGiveFeedback" ).prop( "disabled", false );
            Swal.fire({
              icon: 'error',
              title: 'Unable to submit feedback',
              text: error
            })
          }
        )
      },
      submitGiveFeedbackSeries: function(){
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
          
          this.api('insertFeedback', this.session.id, null, this.escapeAmpersand(JSON.stringify({'feedback': this.session.feedback, 'questions': this.session.questions, 'subsessions': this.session.subsessions}))).then(
            function(res) {
              app.hideComponent('giveFeedbackSeries')
              app.showComponent('completedFeedback')
              if (app.session.certificate) app.showComponent('certificate')
              document.cookie = app.session.id+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; //need to clear once submitted instead
              app.certificate.id = app.session.id
              app.session = ''
              window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
            },
            function(error) {
              $( "#submitGiveFeedbackSeries" ).html("Retry give feedback?")
              $( "#submitGiveFeedbackSeries" ).prop( "disabled", false );
              Swal.fire({
                icon: 'error',
                title: 'Unable to submit feedback',
                text: error
              })
            }
          )
      },
      selectRegion: function(){
        if (this.certificate.region == 0){
          $( "#certOrgSelect" ).prop('hidden',true)
          $( "#certOrg" ).prop('hidden',false)
          this.certificate.org = ''
        } else {
          $( "#certOrgSelect" ).prop('hidden',false)
          $( "#certOrg" ).prop('hidden',true)
          let centreList = regionHospitals[this.certificate.region]
          document.getElementById("certOrgSelect").length = 0; //removes centres already in list if region changed
          document.getElementById("certOrgSelect").options[0]=new Option("Select your centre from list:","");
          var menuOption;
          for(var i = 1; i < centreList.length+1; i++) { //iterates over centreList and adds each item to the centre drop down menu
            menuOption = document.createElement("option")
            menuOption.text = centreList[i-1]
            menuOption.value = centreList[i-1]
            document.getElementById("certOrgSelect").add(menuOption)
          }
          menuOption = document.createElement("option")
          menuOption.text = 'Other...'
          menuOption.value = 0
          document.getElementById("certOrgSelect").add(menuOption)
        }
      },
      selectOrg: function(){
        if (this.certificate.org == 0) {
          $( "#certOrgSelect" ).prop('hidden',true)
          $( "#certOrg" ).prop('hidden',false)
          this.certificate.org = ''
        } else {
          $( "#certOrgSelect" ).prop('hidden',false)
          $( "#certOrg" ).prop('hidden',true)
        }
      },
      downloadCertificate: function(){
        $( "#getCertificateForm" ).addClass("was-validated")
        if (this.certificate.name == '' || this.certificate.org == '') {
          Swal.fire({
            icon: 'error',
            text: 'Please enter your name and organisation'
          })
          return
        }
        $( "#certRegionSelect" ).prop('disabled',true)
        $( "#certOrgSelect" ).prop('disabled',true)
        $( "#certOrg" ).prop('readonly',true)
        $( "#certName" ).prop('readonly',true)
        document.getElementById('fetchCertificateForm').submit()
      },
      //view feedback
      loadPinViewFeedback: function(){
        console.log("Start loadPinViewFeedback...")

        Swal.fire({
          title: 'View feedback',
          html:
            'You will need your session ID and PIN which you can find in the email you received when your session was created.<br>'+
            '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
            '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          preConfirm: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.pin = document.getElementById('swalFormPin').value
          }
        }).then((result) => {
          if (result.isConfirmed) {
            if (!app.session.id || !app.session.pin) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and PIN.'
              })
              return
            }
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?view="+this.session.id)
            app.loadViewFeedback()
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      loadViewFeedback: function(){
        console.log("Start loadViewFeedback...")
        this.session.pin = this.session.pin.trimEnd()
        $( "#submitPin" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitPin" ).prop( "disabled", true );
        
        this.api("fetchFeedback", this.session.id, this.session.pin, null).then(
          function(res) {
            app.session.name = res.name
            app.session.title = res.title
            app.session.date = res.date
            app.session.feedback.positive = res.positive
            app.session.feedback.negative = res.negative
            let sum = 0
            for (let score of res.score) sum += parseInt(score)
            app.session.feedback.score = Math.round((sum/res.score.length)*10)/10
            if (Number.isNaN(app.session.feedback.score)) app.session.feedback.score = "-"
            app.session.questions = res.questions
            if (res.subsessions) {
              for (let sub of res.subsessions) {
                let parsed = JSON.parse(sub)
                let sum = 0
                for (let score of parsed.score) sum += parseInt(score)
                parsed.score = Math.round((sum/parsed.score.length)*10)/10
                if (Number.isNaN(parsed.score)) parsed.score = "-"
                app.session.subsessions.push(parsed)
              }
            }
            app.hideComponent('welcome')
            app.showComponent('viewFeedback')
          },
          function(error) {
            Swal.fire({
              icon: 'error',
              text: error
            })
            $( "#submitPin" ).html("Retry view feedback?")
            $( "#submitPin" ).prop( "disabled", false )
          }
        )
      },
      //view attendance
      loadPinViewAttendance: function(){
        console.log("Start loadPinViewAttendance...")

        Swal.fire({
          title: 'View attendance register',
          html:
            'You will need your session ID and PIN which you can find in the email you received when your session was created.<br>'+
            '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
            '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          preConfirm: () => {
            app.session.id = document.getElementById('swalFormId').value,
            app.session.pin = document.getElementById('swalFormPin').value
          }
        }).then((result) => {
          if (result.isConfirmed) {
            if (!app.session.id || !app.session.pin) {
              Swal.fire({
                icon: 'error',
                text: 'Please enter a session ID and PIN.'
              })
              return
            }
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?attendance="+this.session.id)
            app.loadViewAttendance()
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      loadViewAttendance: function(){
        console.log("Start loadViewAttendance...")
        this.session.pin = this.session.pin.trimEnd()
        $( "#submitPin" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitPin" ).prop( "disabled", true );
        
        this.api("fetchAttendance", this.session.id, this.session.pin, null).then(
          function(res) {
            app.session.name = res.name
            app.session.title = res.title
            app.session.date = res.date
            app.session.attendees = res.attendees
            app.session.attendeeCount = res.attendeeCount
            app.hideComponent('welcome')
            app.showComponent('viewAttendance')
          },
          function(error) {
            Swal.fire({
              icon: 'error',
              text: error
            })
          }
        )
      },
      //utilities
      api: function(route, id, pin, data){
        return new Promise(function(resolve, reject) {
          let timeoutDuration = 10000
          setTimeout(function() {
            reject(new Error("Error: API timeout after "+timeoutDuration/1000+"s"))
          }, timeoutDuration)
          let req = new XMLHttpRequest();
          req.open('POST', 'api-v4/?'+route);
          req.onload = function() {
            if (req.status == 200) {
              try {
                console.log("API response",JSON.parse(req.response))
              }
              catch(e) {
                console.log("API response", req.response)
                console.error("Error outputting API response as parsed object", e)
              }
              resolve(JSON.parse(req.response));
            } else {
              try {
                console.log("API error",JSON.parse(req.response))
              }
              catch(e) {
                console.log("API error", req.response)
                console.error("Error outputting API error as parsed object", e)
              }
              reject(JSON.parse(req.response));
            }
          };
          req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          req.send("route="+route+"&id="+id+"&pin="+pin+"&data="+data);
          console.log("API request", {route}, {id}, {pin}, data)
        });
      },
      findMySessions: function(){
        console.log("Start findMySessions...")
        Swal.fire({
          title: 'Find your feedback sessions',
          html:
            'Enter your email below and we\'ll email you with a list of any sessions you\'ve created previously.<br>'+
            '<input id="swalFormEmail" placeholder="Email address" autocomplete="off" class="swal2-input">',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          preConfirm: () => {
            app.session.email = document.getElementById('swalFormEmail').value
          }
        }).then((result) => {
          if (result.isConfirmed) {
            this.api("findMySessions", null, null, JSON.stringify(this.session.email)).then(
              function(res) {
                Swal.fire({
                  icon: 'success',
                  text: res
                })
              },
              function(error) {
                Swal.fire({
                  icon: 'error',
                  text: error
                })
              }
            )
          }
        })
      },
      escapeAmpersand: function(str) {
        return str.replace(/&/g, "%26")
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
      showQuote: function(){
        const random = Math.floor(Math.random() * quoteList.length);
        this.quote = "<q>" + quoteList[random].quote + "</q><br><i>– " + quoteList[random].cite + "</i><br><a onclick='app.showQuote()'><i id='reload' class='fas fa-redo'></i></a>"
      }
    },
    mounted: function() {
      if (document.cookie) {
          try{    
            let raw = document.cookie
            let split = raw.split("; ")
            let sliced = []
            for (let sp of split) sliced.push(sp.slice(7))
            for (let sl of sliced) this.cookies.push(JSON.parse(sl))
          } catch (e) {
              console.log(e)
          }
      }
      let args = window.location.search
      if (args.includes("?view=")) { //view feedback
        this.session.id = args.replace("?view=","")
        console.log("Autodirect view feedback for session: " + this.session.id)
        this.hideComponent("loader")
        this.showComponent("welcome")
        this.loadPinViewFeedback()
      } else if (args.includes("?edit=")) { //update details
        this.session.id = args.replace("?edit=","")
        console.log("Autodirect edit feedback for session: " + this.session.id)
        this.hideComponent("loader")
        this.showComponent("welcome")
        this.loadPinUpdateDetails()
      } else if (args.includes("?resetPIN=")) { //reset pin
        this.session.id = args.replace("?resetPIN=","")
        console.log("Autodirect reset PIN for session: " + this.session.id)
        this.hideComponent("loader")
        this.showComponent("welcome")
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
        this.resetPIN()
      } else if (args.includes("?notifications=")) { //update notification preferences
        this.session.id = args.replace("?notifications=","")
        console.log("Autodirect update notification preferences for session: " + this.session.id)
        window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/")
        this.hideComponent("loader")
        this.showComponent('welcome')
        this.loadNotificationPreference()
      } else if (args.includes("?attendance=")) { //view attendance register
          this.session.id = args.replace("?attendance=","")
          console.log("Autodirect view attendance for session: " + this.session.id)
          this.hideComponent("loader")
          this.showComponent('welcome')
          this.loadPinViewAttendance()
      } else if (args.includes("?createSessionSeries")) { //create new session series
          this.hideComponent("loader")
          this.showComponent("createSessionSeries")
      } else if (args.includes("?createSession")) { //create new session
          this.hideComponent("loader")
          this.showComponent("createSession")
      } else if (args.includes("Created")) { //don't reload the completed create session page
          window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/") 
          this.hideComponent("loader")
          this.showComponent('welcome')
      } else { //give feedback
        this.session.id = args.replace("?","")
        if (this.session.id){
          this.session.id = this.session.id.replace("/","")
          console.log("Autodirect give feedback for session: " + this.session.id)
          this.loadGiveFeedback()
        } else { //just show the welcome page
          this.hideComponent('loader')
          this.showComponent('welcome')
        }
      }
      this.showQuote()
    }
  })
  
window.onbeforeunload = function() { app.saveProgress()}