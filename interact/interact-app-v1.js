console.log ("interact-app-v1.js running...")

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
      quote: "Tell me and I forget, teach me and I may remember, involve me and I learn.",
      cite: "Benjamin Franklin"
  },
  {
      quote: "In learning you will teach, and in teaching you will learn.",
      cite: "Phil Collins"
  }
]

var app = new Vue({
    el: '#app',
    data() {
      return {
        show: {
          welcome: false,
          loader: true,
          createInteractSession: false,
          interactSessionCreated: false,
          interact: false,
          interactView: false
        },
        invite: {
          link: '',
          qr: '',
          view: '',
        },
        showCopyBtns: true,
        quote: '',
        session: {
          id: '',
          name: '',
          email: '',
          title: '',
          pin: '',
          questions: [],
          currentQuestionIndex: 0
        },
        question: {
          title: '',
          type: '',
          options: [],
          response: '',
          index: -1
        }
      }
    },
    methods: {
      //utilities
      api: function(route, id, pin, data){
        return new Promise(function(resolve, reject) {
          let timeoutDuration = 10000
          setTimeout(function() {
            reject(new Error("Error: API timeout after "+timeoutDuration/1000+"s"))
          }, timeoutDuration)
          let req = new XMLHttpRequest();
          req.open('POST', 'api-v5/?'+route);
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
      escapeAmpersand: function(str) {
        return str.replace(/&/g, "%26")
      },
      showComponent: function(comp){
        console.log('Showing: [' + comp + ']')
        this.show[comp] = true
        window.scrollTo({top:0, left:0, behavior: 'smooth'})
        if (comp == "createSession") window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/interact/?createSession")
        if (comp == "createSessionSeries") window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/interact/?createSessionSeries")
      },
      hideComponent: function(comp){
        if (comp == "all") { for (let c in this.show) this.hideComponent(c) }
        else { this.show[comp] = false }
      },
      showQuote: function(){
        const random = Math.floor(Math.random() * quoteList.length);
        this.quote = "<q>" + quoteList[random].quote + "</q><br><i>– " + quoteList[random].cite + "</i><br><a onclick='app.showQuote()'><i id='reload' class='fas fa-redo'></i></a>"
      },
      findMySessions: function(){
        console.log('findMySessions to do')
      },
      //session creation
      createQuestion: function(){
        console.log('Start createQuestion...')
        $( "#createSessionForm" ).addClass("was-validated") //add validation formatting to form
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
        $( "#createquestionForm" ).removeClass("was-validated")
        $("#submitCreatequestion" ).html("Add question")
        $("#submitCreatequestion" ).prop("disabled", false )
        $('#addquestion').modal('hide');
      },
      editQuestion: function(i){
        console.log('Start editquestion...')
        this.question.title = this.session.questions[i].title
        this.question.type = this.session.questions[i].type
        for (let o in this.session.questions[i].options) this.question.options += this.session.questions[i].options[o].title + ";"
        this.question.index = i
        $('#addquestion').modal({backdrop: "static"})
        $( "#submitCreatequestion" ).html("Update question")
      },
      removeQuestion: function(i){
        console.log("Start removequestion...")
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
      createSession: function(){
        console.log('Start createsession...')
        $( "#createsessionForm" ).addClass("was-validated")
        if (this.session.title == '' || this.session.name == '' || this.session.email == '') return
        $( "#submitCreatesession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#submitCreatesession" ).prop( "disabled", true );
        this.api('insertsession', null, null, this.escapeAmpersand(JSON.stringify(this.session))).then(
          function(res) {
            app.session.id = res.id
            app.session.pin = res.pin
            app.session.date = res.date
            let tempLink = 'https://learnloop.co.uk/interact/?' + app.session.id
            app.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
            app.invite.view = 'https://learnloop.co.uk/interact/?view=' + app.session.id
            app.invite.qr = 'https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=' + tempLink + '&choe=UTF-8&chld=h'
            app.hideComponent('createsession')
            app.showComponent('sessionCreated')
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/interact/?sessionCreated")
          },
          function(error) {
            $( "#submitCreatesession" ).html("Retry create session?")
            $( "#submitCreatesession" ).prop( "disabled", false )
            Swal.fire({
              icon: 'error',
              title: 'Unable to create session',
              text: error
            })
          }
        )
      },
      //update details
      loadPinUpdateDetails: function(){
        console.log('loadPinUpdateDetails todo')
      },
      closeSession: function(){
        console.log('closeSession todo')
      },
      resetPIN: function(){
        console.log('resetPIN todo')
      },
      //join
      joinSession: function(){
        console.log('joinSession todo')
      },
      fetchDetails: function(){
        this.api('fetchDetails',this.session.id, null, null).then(
          function(res) {
            if (res.closed) {
              Swal.fire({
                icon: 'error',
                text: 'This interact session has been closed by the facilitator.'
              })
              $( "#joinSession" ).html("Join")
              $( "#joinSession" ).prop( "disabled", false );
              return
            }
            app.session.title = res.title
            app.session.name = res.name
            app.session.questions = res.questions
            for (let question of app.session.questions) question.options = JSON.parse(question.options)
            app.hideComponent('loader')
            app.hideComponent('welcome')
            app.showComponent('join')
          },
          function(error) {
            app.hideComponent('loader')
            app.showComponent('welcome')
            $( "#joinSession" ).html("Join")
            $( "#joinSession" ).prop( "disabled", false );
            Swal.fire({
              icon: 'error',
              title: 'Unable to load interact session',
              text: error
            })
            return
          }
        )
      },
      insertSubmission: function(index){
        console.log("Start insertSubmission...")
        let response = []
        if (this.session.questions[index].type == "text") {
          if (this.session.questions[index].response.length<1){
            Swal.fire({
              icon: 'error',
              title: 'Unable to submit your response',
              text: 'Your response is blank'
            })
            return
          } else {
            response.push(this.session.questions[index].response)
          }
        }
        if (this.session.questions[index].type == "checkbox") {
          for (let option of this.session.questions[index].options) if (option.selected) response.push(option.title)
          if (!response.length){
            Swal.fire({
              icon: 'error',
              title: 'Unable to submit your response',
              text: 'Your response is blank'
            })
            return
          }
        }
        if (this.session.questions[index].type == "select") {
          if (!this.session.questions[index].response){
            Swal.fire({
              icon: 'error',
              title: 'Unable to submit your response',
              text: 'Your response is blank'
            })
            return
          } else {
            response.push(this.session.questions[index].response)
          }
        }

        $( "#submitQuestion" ).html("Please wait...")
        $( "#submitQuestion" ).prop( "disabled", true );
        this.api('insertSubmission', this.session.questions[index].id, null, JSON.stringify(this.session.questions[index])).then(
          function(res) {
            Swal.fire({
              icon: 'success',
              title: 'Submission successful'
            })
            let multipleSubmissions = true //todo
            if (multipleSubmissions){
              //todo: clear previous submission
              $( "#submitQuestion" ).html("Submit")
              $( "#submitQuestion" ).prop( "disabled", false )
            } else {
              //todo: lock form
              $( "#submitQuestion" ).html("Submit")
              $( "#submitQuestion" ).prop( "disabled", true )
            }
          },
          function(error){
            Swal.fire({
              icon: 'error',
              title: 'Unable to submit your response',
              text: error
            })
            $( "#submitQuestion" ).html("Retry submit?")
            $( "#submitQuestion" ).prop( "disabled", false )
          }
        )
      },
      //view
      loadPinViewInteract: function(){
        console.log("Start loadPinView...")

        Swal.fire({
          title: 'View interact submissions',
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
            window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/interact/?view="+this.session.id)
            app.fetchViewDetails()
          }
        })
        document.getElementById('swalFormId').value = this.session.id
      },
      fetchViewInteractDetails: function(){
        //currently working on this route...
        this.api('fetchViewDetails', this.session.id, this.session.pin, null).then(
          function(res) {
            if (res.closed) {
              Swal.fire({
                icon: 'warning',
                text: 'Please note this interact session has been closed by the facilitator and is not accepting submissions from attendees.'
              })
            }
            app.session.title = res.title
            app.session.name = res.name
            app.session.questions = res.questions
            for (let question of app.session.questions) question.options = JSON.parse(question.options)
            app.hideComponent('loader')
            app.hideComponent('welcome')
            app.showComponent('view')
          },
          function(error) {
            app.hideComponent('loader')
            app.showComponent('welcome')
            $( "#viewExistingsession" ).html("View")
            $( "#viewExistingsession" ).prop( "disabled", false );
            Swal.fire({
              icon: 'error',
              title: 'Unable to view interact session',
              text: error
            })
            return
          }
        )
      },
      checkIfNewSubmissions: function(){
        //if true fetchViewQuestionSubmissions
        //await api response then if update On then timeout then repeat
      },
      fetchViewInteractQuestionSubmissions: function(){

      },
      //view submissions
      loadPinViewSubmissions: function(){
        console.log('loadPinViewSubmissions todo')
      }
    },
    mounted: function() {
      this.hideComponent('loader')
      this.showComponent('welcome')
      this.showQuote()
    }
  })